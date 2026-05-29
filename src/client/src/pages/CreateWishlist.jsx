import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useAuth } from '../AuthContext'
import { createWishlist } from '../api/api'
import './css/WishlistsPage.css'
import './css/LoginPage.css'

export default function CreateWishlist() {
  const { user } = useAuth()
  const navigate = useNavigate()

  const [name, setName] = useState('')
  const [description, setDescription] = useState('')
  const [isPublic, setIsPublic] = useState(false)
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  async function handleCreate(e) {
    e.preventDefault()
    setError('')

    if (!user?.id) {
      setError('You are not authorized')
      return
    }

    const trimmedName = name.trim()
    if (!trimmedName) {
      setError('Enter a wishlist name')
      return
    }

    setLoading(true)
    try {
      await createWishlist({
        user_id: user.id,
        name: trimmedName,
        description: description.trim() || null,
        is_public: isPublic ? 1 : 0,
      })
      navigate('/wishlists')
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
          <Link to="/profile">Личный кабинет</Link>
        </div>
      </nav>

      <main className="wishlists-container">
        <h1>Создать вишлист</h1>

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
            <label style={{ flexDirection: 'row', alignItems: 'center', gap: '0.5rem', cursor: 'pointer' }}>
              <input
                type="checkbox"
                checked={isPublic}
                onChange={e => setIsPublic(e.target.checked)}
              />
              Публичный вишлист
            </label>
          </div>

          <button type="submit" className="btn-submit" disabled={loading}>
            {loading ? 'Создаём...' : 'Создать'}
          </button>
        </form>
      </main>
    </div>
  )
}
