import { useState, useEffect } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { useAuth } from '../AuthContext'
import { getUserWishlists } from '../api/api'
import './css/WishlistsPage.css'

export default function WishlistsPage() {
  const { user } = useAuth()
  const location = useLocation()
  const [wishlists, setWishlists] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    if (!user?.id) {
      setLoading(false)
      return
    }
    fetchWishlists(user.id)
  }, [user?.id, location.key])

  async function fetchWishlists(userId) {
    try {
      const data = await getUserWishlists(userId)
      setWishlists(Array.isArray(data) ? data : [])
    } catch (error) {
      console.error('Ошибка загрузки вишлистов:', error)
    } finally {
      setLoading(false)
    }
  }

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
        <h1>Мои вишлисты</h1>

        {loading ? (
          <p className="loading">Загрузка...</p>
        ) : wishlists.length === 0 ? (
          <div className="empty-state">
            <div className="add-card add-card-large">
              <div className="add-icon">+</div>
              <Link to="/create-wishlist" className="add-text">Создать вишлист</Link>
            </div>
          </div>
        ) : (
          <div className="wishlists-grid">
            {wishlists.map(wishlist => (
              <Link
                key={wishlist.id}
                to={`/wishlists/${wishlist.id}`}
                className="wishlist-card"
              >
                <h3>{wishlist.name}</h3>
                <p>{wishlist.description || 'Нет описания'}</p>
                <span className="items-count">Предметов: {Number(wishlist.items_count) || 0}</span>
              </Link>
            ))}

            <Link to="/create-wishlist" className="add-card">
              <div className="add-icon">+</div>
              <span className="add-text">Создать</span>
            </Link>
          </div>
        )}
      </main>
    </div>
  )
}
