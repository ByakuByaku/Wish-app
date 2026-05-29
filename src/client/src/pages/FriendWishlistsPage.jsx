import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { getFriend, getUserWishlists } from '../api/api'
import './css/WishlistsPage.css'

export default function FriendWishlistsPage() {
  const { userId } = useParams()
  const friendId = Number(userId)

  const [friend, setFriend] = useState(null)
  const [wishlists, setWishlists] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    if (!friendId) return

    let cancelled = false

    async function load() {
      setLoading(true)
      setError('')
      try {
        const [profile, lists] = await Promise.all([
          getFriend(friendId),
          getUserWishlists(friendId),
        ])
        if (cancelled) return
        setFriend(profile)
        setWishlists(Array.isArray(lists) ? lists : [])
      } catch (err) {
        if (!cancelled) setError(err.message)
      } finally {
        if (!cancelled) setLoading(false)
      }
    }

    load()
    return () => {
      cancelled = true
    }
  }, [friendId])

  return (
    <div>
      <nav className="nav">
        <Link to="/" className="nav-logo">Wishlist</Link>
        <div className="nav-links">
          <Link to="/wishlists">Вишлист</Link>
          <Link to="/friends">Друзья</Link>
          <Link to="/profile">Личный кабинет</Link>
        </div>
      </nav>

      <main className="wishlists-container">
        <div style={{ marginBottom: '10px' }}>
          <Link to="/friends" style={{ color: '#777', fontSize: '0.9rem' }}>
            ← Назад к друзьям
          </Link>
        </div>

        <h1>Вишлисты {friend?.name || 'друга'}</h1>

        {loading ? (
          <p className="loading">Загрузка...</p>
        ) : error ? (
          <p className="loading">{error}</p>
        ) : wishlists.length === 0 ? (
          <div className="empty-state">
            <p className="loading">У друга пока нет вишлистов</p>
          </div>
        ) : (
          <div className="wishlists-grid">
            {wishlists.map(wishlist => (
              <Link
                key={wishlist.id}
                to={`/friends/${friendId}/wishlists/${wishlist.id}`}
                className="wishlist-card"
              >
                <h3>{wishlist.name}</h3>
                <p>{wishlist.description || 'Нет описания'}</p>
                <span className="items-count">Предметов: {Number(wishlist.items_count) || 0}</span>
              </Link>
            ))}
          </div>
        )}
      </main>
    </div>
  )
}
