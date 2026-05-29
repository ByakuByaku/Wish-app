import { useState } from 'react'
import { Link, useNavigate, useParams } from 'react-router-dom'
import { addItem } from '../api/api'
import './css/WishlistsPage.css'
import './css/LoginPage.css'

export default function CreateWishlistItemPage() {
  const { id } = useParams()
  const navigate = useNavigate()

  const [name, setName] = useState('')
  const [description, setDescription] = useState('')
  const [price, setPrice] = useState('')
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  async function handleCreate(e) {
    e.preventDefault()
    setError('')

    const trimmedName = name.trim()
    if (!trimmedName) {
      setError('Enter an item name')
      return
    }

    const priceValue = price.trim() === '' ? null : Number(price)
    if (price.trim() !== '' && (Number.isNaN(priceValue) || priceValue < 0)) {
      setError('Enter a valid price')
      return
    }

    setLoading(true)
    try {
      await addItem(id, {
        name: trimmedName,
        description: description.trim() || null,
        price: priceValue,
      })
      navigate(`/wishlists/${id}`)
    } catch (err) {
      setError(err.message)
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
        <div style={{ marginBottom: '10px' }}>
          <Link to={`/wishlists/${id}`} style={{ color: '#777', fontSize: '0.9rem' }}>
            ← Назад к вишлисту
          </Link>
        </div>

        <h1>Добавить предмет</h1>

        <form onSubmit={handleCreate} style={{ maxWidth: 420 }}>
          {error && <div className="error">{error}</div>}

          <div className="form-group">
            <label>Название</label>
            <input
              type="text"
              value={name}
              onChange={e => setName(e.target.value)}
              required
            />
          </div>

          <div className="form-group">
            <label>Описание (необязательно)</label>
            <input
              type="text"
              value={description}
              onChange={e => setDescription(e.target.value)}
            />
          </div>

          <div className="form-group">
            <label>Цена (необязательно)</label>
            <input
              type="number"
              min="0"
              step="0.01"
              value={price}
              onChange={e => setPrice(e.target.value)}
            />
          </div>

          <button type="submit" className="btn-submit" disabled={loading}>
            {loading ? 'Добавляем...' : 'Добавить'}
          </button>
        </form>
      </main>
    </div>
  )
}
