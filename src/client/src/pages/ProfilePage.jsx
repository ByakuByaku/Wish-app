import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useAuth } from '../AuthContext'
import { getMe, updateUser } from '../api/api'
import './css/ProfilePage.css'

export default function ProfilePage() {
  const navigate = useNavigate()
  const { user, signOut, updateStoredUser } = useAuth()
  const [profile, setProfile] = useState(user)
  const [loading, setLoading] = useState(false)
  const [editing, setEditing] = useState(false)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState('')
  const [message, setMessage] = useState('')
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [age, setAge] = useState('')
const [password, setPassword] = useState('')
  function handleLogout() {
    signOut()
    navigate('/login')
  }

  function fillForm(data) {
    setName(data?.name || '')
    setEmail(data?.email || user?.email || '')
    setAge(data?.age || '')
  }

  async function handleSaveProfile(e) {
    e.preventDefault()
    setError('')
    setMessage('')

    if (!name.trim() || !email.trim()) {
      setError('Заполните имя и email')
      return
    }

    setSaving(true)
    try {
      const payload = {
        name: name.trim(),
        email: email.trim(),
        age: age === '' ? null : Number(age),
      }

      if (password.trim()) {
        payload.password = password.trim()
      }

      await updateUser(user.id, payload)
      const data = await getMe(user.id)
      setProfile(data)
      updateStoredUser({ id: data.id, email: data.email, role: data.role })
      fillForm(data)
      setEditing(false)
      setMessage('Данные обновлены')
      setPassword('')
    } catch (err) {
      setError(err.message)
    } finally {
      setSaving(false)
    }
  }

  useEffect(() => {
    let cancelled = false

    async function load() {
      if (!user?.id) return
      setLoading(true)
      try {
        const data = await getMe(user.id)
        if (!cancelled) {
          setProfile(data)
          fillForm(data)
        }
      } catch (e) {
        if (!cancelled) {
          setProfile(user)
          fillForm(user)
        }
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
              <p>{loading ? 'Загрузка...' : (profile?.name || '-')}</p>
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
            <button onClick={handleLogout} className="btn-logout">
              Выход
            </button>
          </div>

          <div className="profile-actions" style={{ marginTop: '1rem' }}>
            <button
              type="button"
              onClick={() => {
                fillForm(profile)
                setEditing(prev => !prev)
                setError('')
                setMessage('')
              }}
              className="btn-secondary"
            >
              {editing ? 'Отменить изменение' : 'Изменить информацию'}
            </button>
          </div>

          {message && <p className="text-muted" style={{ marginTop: '1rem' }}>{message}</p>}
          {error && <p className="error" style={{ marginTop: '1rem' }}>{error}</p>}

          {editing && (
            <form onSubmit={handleSaveProfile} className="profile-edit-form">
              <div className="info-group">
                <label>Имя:</label>
                <input value={name} onChange={e => setName(e.target.value)} required />
              </div>
              <div className="info-group">
                <label>Email:</label>
                <input type="email" value={email} onChange={e => setEmail(e.target.value)} required />
              </div>
              <div className="info-group">
                <label>Возраст:</label>
                <input type="number" min="0" value={age} onChange={e => setAge(e.target.value)} />
              </div>
              <div>
                <label>Новый пароль:</label>
                <input type="password" value={password} onChange={e => setPassword(e.target.value)} />
              </div>
              <button type="submit" className="btn-logout" disabled={saving}>
                {saving ? 'Сохраняем...' : 'Сохранить'}
              </button>
            </form>
          )}
        </div>
      </main>
    </div>
  )
}
