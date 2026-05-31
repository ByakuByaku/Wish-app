import { useEffect, useMemo, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { useAuth } from '../AuthContext'
import { getWishlist, getItems, reserveItem, unreserveItem } from '../api/api'
import './css/WishlistsPage.css'
import './css/LoginPage.css'

export default function FriendWishlistPage() {
  const { userId, wishlistId } = useParams()
  const { user } = useAuth()
  const friendId = Number(userId)

  const [wishlist, setWishlist] = useState(null)
  const [items, setItems] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [actionId, setActionId] = useState(null)
  const [search, setSearch] = useState('')

  const filteredItems = useMemo(() => {
    const query = search.trim().toLowerCase()
    if (!query) return items

    return items.filter(item => (
      item.name?.toLowerCase().includes(query) ||
      item.description?.toLowerCase().includes(query)
    ))
  }, [items, search])

  async function loadItems() {
    const [wishlistData, itemsData] = await Promise.all([
      getWishlist(wishlistId),
      getItems(wishlistId),
    ])
    setWishlist(wishlistData)
    setItems(Array.isArray(itemsData) ? itemsData : [])
  }

  useEffect(() => {
    if (!wishlistId) return

    let cancelled = false

    async function load() {
      setLoading(true)
      setError('')
      try {
        await loadItems()
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
  }, [wishlistId])

  async function handleReserve(itemId) {
    setActionId(itemId)
    setError('')
    try {
      await reserveItem(wishlistId, itemId)
      await loadItems()
    } catch (err) {
      setError(err.message)
    } finally {
      setActionId(null)
    }
  }

  async function handleUnreserve(itemId) {
    setActionId(itemId)
    setError('')
    try {
      await unreserveItem(wishlistId, itemId)
      await loadItems()
    } catch (err) {
      setError(err.message)
    } finally {
      setActionId(null)
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
          <Link
            to={`/friends/${friendId}/wishlists`}
            style={{ color: '#777', fontSize: '0.9rem' }}
          >
            ← Назад к вишлистам друга
          </Link>
        </div>

        {!loading && wishlist && <h1>{wishlist.name}</h1>}

        {error && <div className="error">{error}</div>}

        {loading ? (
          <p className="loading">Загрузка...</p>
        ) : items.length === 0 ? (
          <div className="empty-state">
            <p className="loading">В этом вишлисте пока нет предметов</p>
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
            {filteredItems.map(item => {
              const reservedBy = item.reserved_by ? Number(item.reserved_by) : null
              const isMine = reservedBy === Number(user?.id)
              const isReserved = reservedBy != null

              return (
                <div key={item.id} className="wishlist-card">
                  <h3>{item.name}</h3>
                  <p>{item.description || 'Нет описания'}</p>
                  {item.price != null && (
                    <span className="items-count">{item.price} ₽</span>
                  )}

                  {isReserved && !isMine && (
                    <span className="items-count" style={{ color: '#777' }}>
                      Зарезервировано
                    </span>
                  )}

                  {isMine && (
                    <span className="items-count">Вы зарезервировали</span>
                  )}

                  {!isReserved && (
                    <button
                      type="button"
                      className="btn-submit"
                      style={{ marginTop: '0.75rem' }}
                      disabled={actionId === item.id}
                      onClick={() => handleReserve(item.id)}
                    >
                      {actionId === item.id ? '...' : 'Зарезервировать'}
                    </button>
                  )}

                  {isMine && (
                    <button
                      type="button"
                      className="btn-submit"
                      style={{ marginTop: '0.75rem', background: '#f5f5f5', color: '#777' }}
                      disabled={actionId === item.id}
                      onClick={() => handleUnreserve(item.id)}
                    >
                      {actionId === item.id ? '...' : 'Снять резерв'}
                    </button>
                  )}
                </div>
              )
            })}
            </div>
          )}
          </>
        )}
      </main>
    </div>
  )
}
