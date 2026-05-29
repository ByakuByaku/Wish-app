import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { getWishlist, getItems } from '../api/api'
import './css/WishlistsPage.css'

export default function WhishlistPage() {
  const { id } = useParams()

  const [wishlist, setWishlist] = useState(null)
  const [items, setItems] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  useEffect(() => {
    if (!id) return

    let cancelled = false

    async function load() {
      setLoading(true)
      setError('')
      try {
        const [wishlistData, itemsData] = await Promise.all([
          getWishlist(id),
          getItems(id),
        ])

        if (cancelled) return
        setWishlist(wishlistData)
        setItems(Array.isArray(itemsData) ? itemsData : [])
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
  }, [id])

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
          <Link to="/wishlists" style={{ color: '#777', fontSize: '0.9rem' }}>
            ← Назад к списку
          </Link>
        </div>

        {!loading && wishlist && <h1>{wishlist.name}</h1>}

        {loading ? (
          <p className="loading">Загрузка...</p>
        ) : error ? (
          <p className="loading">{error}</p>
        ) : items.length === 0 ? (
          <div className="empty-state">
            <div className="add-card add-card-large">
              <div className="add-icon">+</div>
              <Link to={`/wishlists/${id}/add-item`} className="add-text">Добавить предмет</Link>
            </div>
          </div>
        ) : (
          <div className="wishlists-grid">
            {items.map(item => (
              <div key={item.id} className="wishlist-card">
                <h3>{item.name}</h3>
                <p>{item.description || 'Нет описания'}</p>
                {item.price != null && (
                  <span className="items-count">{item.price} ₽</span>
                )}
              </div>
            ))}
            <Link to={`/wishlists/${id}/add-item`} className="add-card">
              <div className="add-icon">+</div>
              <span className="add-text">Добавить предмет</span>
            </Link>
          </div>
        )}
      </main>
    </div>
  )
}
