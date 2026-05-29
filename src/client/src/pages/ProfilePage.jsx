import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../AuthContext'
import { getMe } from '../api/api'
import './css/ProfilePage.css'


export default function ProfilePage() {
  const navigate = useNavigate()
  const { user, signOut } = useAuth()
  const [profile, setProfile] = useState(user)
  const [loading, setLoading] = useState(false)

  function handleLogout() {
    signOut()
    navigate('/login')
  }

  useEffect(() => {
    let cancelled = false

    async function load() {
      if (!user?.id) return
      setLoading(true)
      try {
        const data = await getMe(user.id)
        if (!cancelled) setProfile(data)
      } catch (e) {
        // Если профиль не загрузился (например, токен истёк), покажем то, что есть в контексте
        if (!cancelled) setProfile(user)
      } finally {
        if (!cancelled) setLoading(false)
      }
    }

    load()
    return () => {
      cancelled = true
    }
  }, [user?.id])

  if (!user) {
    return (
      <div>
        <nav className="nav">
          <a href="/" className="nav-logo">Wishlist</a>
        </nav>
        <main className="profile-container">
          <p className="error">Вы не авторизованы</p>
        </main>
      </div>
    )
  }

  return (
    <div>
      <nav className="nav">
        <a href="/" className="nav-logo">Wishlist</a>
        <div className="nav-links">
          <a href="/wishlists">Вишлист</a>
          <a href="/friends">Друзья</a>
          <a href="/profile">Личный кабинет</a>
        </div>
      </nav>

      <main className="profile-container">
        <div className="profile-card">
          <h1>Личный кабинет</h1>
          
          <div className="profile-info">
            <div className="info-group">
              <label>Имя:</label>
              <p>{loading ? 'Загрузка...' : (profile?.name || '—')}</p>
            </div>
            
            <div className="info-group">
              <label>Email:</label>
              <p>{profile?.email || user.email}</p>
            </div>
            
            {profile?.age && (
              <div className="info-group">
                <label>Возраст:</label>
                <p>{profile.age}</p>
              </div>
            )}
            
            <div className="info-group">
              <label>ID:</label>
              <p className="text-muted">{profile?.id ?? user.id}</p>
            </div>
          </div>

          <div className="profile-actions">
            <button 
              onClick={handleLogout}
              className="btn-logout"
            >
              Выход
            </button>
          </div>
        </div>
      </main>
    </div>
  )
}
