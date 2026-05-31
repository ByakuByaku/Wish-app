import { useEffect, useMemo, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { deleteItem, getWishlist, getItems } from '../api/api'
import './css/WishlistsPage.css'

export default function WhishlistPage() {
  const { id } = useParams()

  const [wishlist, setWishlist] = useState(null)
  const [items, setItems] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [search, setSearch] = useState('')
  const [deletingId, setDeletingId] = useState(null)

  const filteredItems = useMemo(() => {
    const query = search.trim().toLowerCase()
    if (!query) return items

    return items.filter(item => (
      item.name?.toLowerCase().includes(query) ||
      item.description?.toLowerCase().includes(query)
    ))
  }, [items, search])

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

  async function handleDeleteItem(itemId) {
    if (!window.confirm('Удалить этот предмет?')) return

    setDeletingId(itemId)
    setError('')
    try {
      await deleteItem(id, itemId)
      setItems(prev => prev.filter(item => item.id !== itemId))
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
          <>
            <input
              className="items-search"
              type="search"
              placeholder="Поиск по предметам"
              value={search}
              onChange={e => setSearch(e.target.value)}
            />

            {filteredItems.length === 0 ? (
              <p className="loading">Ничего не найдено</p>
            ) : (
              <div className="wishlists-grid">
                {filteredItems.map(item => (
                  <div key={item.id} className="wishlist-card">
                    <h3>{item.name}</h3>
                    <p>{item.description || 'Нет описания'}</p>
                    {item.price != null && (
                      <span className="items-count">{item.price} ₽</span>
                    )}
                    <button
                      type="button"
                      className="btn-card-delete"
                      disabled={deletingId === item.id}
                      onClick={() => handleDeleteItem(item.id)}
                    >
                      {deletingId === item.id ? 'Удаляем...' : 'Удалить предмет'}
                    </button>
                  </div>
                ))}
                <Link to={`/wishlists/${id}/add-item`} className="add-card">
                  <div className="add-icon">+</div>
                  <span className="add-text">Добавить предмет</span>
                </Link>
              </div>
            )}
          </>
        )}
      </main>
    </div>
  )
}
