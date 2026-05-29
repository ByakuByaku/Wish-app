import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { useAuth } from '../AuthContext'
import { getFriends, createInvite, useInvite, removeFriend } from '../api/api'
import './css/WishlistsPage.css'
import './css/LoginPage.css'
export default function FriendsPage() {
  const { user } = useAuth()
  const [friends, setFriends] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  const [myCode, setMyCode] = useState('')
  const [inputCode, setInputCode] = useState('')
  const [codeMessage, setCodeMessage] = useState('')

  async function handleCreateInvite() {
  try {
    const data = await createInvite(user.id)
    setMyCode(data.code)
  } catch (err) {
    setCodeMessage(err.message)
  }
}

  async function handleUseInvite() {
  if (!inputCode.trim()) return
  try {
    await useInvite(user.id, inputCode.trim())
    setCodeMessage('Friend added!')
    setInputCode('')
    const data = await getFriends(user.id)
    setFriends(Array.isArray(data) ? data : [])
  } catch (err) {
    setCodeMessage(err.message)
  }
}
  async function handleRemoveFriend(friendId) {
  try {
    await removeFriend(user.id, friendId)
    setFriends(prev => prev.filter(f => f.id !== friendId))
  } catch (err) {
    setError(err.message)
  }
}
  useEffect(() => {
    if (!user?.id) {
      setLoading(false)
      return
    }

    async function load() {
      setLoading(true)
      setError('')
      try {
        const data = await getFriends(user.id)
        setFriends(Array.isArray(data) ? data : [])
      } catch (err) {
        setError(err.message)
      } finally {
        setLoading(false)
      }
    }

    load()
  }, [user?.id])

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
        <h1>Мои друзья</h1>
        <div className="invite-block">
          <div className="invite-section">
            <button onClick={handleCreateInvite} className="btn-invite">
              Создать код
            </button>
            {myCode && (
              <p className="invite-code">Ваш код: <strong>{myCode}</strong></p>
            )}
          </div>
          <div className="invite-section">
            <input
              type="text"
              placeholder="Введите код друга"
              value={inputCode}
              onChange={e => setInputCode(e.target.value)}
              className="invite-input"
            />
            <button onClick={handleUseInvite} className="btn-invite">
              Добавить
            </button>
            {codeMessage && <p className={codeMessage === 'Friend added!' ? 'invite-success' : 'invite-error'}>
              {codeMessage}
            </p>}
          </div>
        </div>
        {loading ? (
          <p className="loading">Загрузка...</p>
        ) : error ? (
          <p className="loading">{error}</p>
        ) : friends.length === 0 ? (
          <div className="empty-state">
            <p className="loading">У вас пока нет друзей</p>
          </div>
        ) : (
          <div className="wishlists-grid">
            {friends.map(friend => (
              <Link
                key={friend.id}
                to={`/friends/${friend.id}/wishlists`}
                className="wishlist-card"
              >
                <h3>{friend.name}</h3>
                <p>{friend.email}</p>
                <span className="items-count">Смотреть вишлисты →</span>
                <button
                  onClick={e => {
                    e.preventDefault()
                    handleRemoveFriend(friend.id)
                  }}
                  className="items-count"
                >
                  Удалить из друзей
                </button>
              </Link>
            ))}
          </div>
        )}
      </main>
    </div>
  )
}
