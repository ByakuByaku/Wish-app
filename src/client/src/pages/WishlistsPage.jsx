import { useState, useEffect } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { useAuth } from '../AuthContext'
import { deleteWishlist, getUserWishlists } from '../api/api'
import './css/WishlistsPage.css'

export default function WishlistsPage() {
  const { user } = useAuth()
  const location = useLocation()
  const [wishlists, setWishlists] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [deletingId, setDeletingId] = useState(null)

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
      setError(error.message)
    } finally {
      setLoading(false)
    }
  }

  async function handleDeleteWishlist(wishlistId) {
    if (!window.confirm('Удалить этот вишлист?')) return

    setDeletingId(wishlistId)
    setError('')
    try {
      await deleteWishlist(wishlistId)
      setWishlists(prev => prev.filter(wishlist => wishlist.id !== wishlistId))
    } catch (err) {
      setError(err.message)
    } finally {
      setDeletingId(null)
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
        {error && <div className="error">{error}</div>}

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
              <div key={wishlist.id} className="wishlist-card">
                <Link to={`/wishlists/${wishlist.id}`} className="card-link-area">
                  <h3>{wishlist.name}</h3>
                  <p>{wishlist.description || 'Нет описания'}</p>
                  <span className="items-count">Предметов: {Number(wishlist.items_count) || 0}</span>
                </Link>
                <button
                  type="button"
                  className="btn-card-delete"
                  disabled={deletingId === wishlist.id}
                  onClick={() => handleDeleteWishlist(wishlist.id)}
                >
                  {deletingId === wishlist.id ? 'Удаляем...' : 'Удалить вишлист'}
                </button>
              </div>
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
