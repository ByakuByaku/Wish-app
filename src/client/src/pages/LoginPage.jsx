import { useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { login, register } from '../api/api'
import { useAuth } from '../AuthContext'
import './css/LoginPage.css'
export default function LoginPage() {
  const [tab, setTab] = useState('login')
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  const { signIn } = useAuth()
  const navigate = useNavigate()

  // Поля
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [name, setName] = useState('')
  const [age, setAge] = useState('')

  function getTabClass(tabName) {
    return tab === tabName ? 'tab tab-active' : 'tab'
  }

  async function handleLogin(e) {
    e.preventDefault()
    setError('')
    setLoading(true)
    try {
      const data = await login(email, password)
      // data = { token }
      // Декодируем payload из токена чтобы получить user_id
      const payload = JSON.parse(atob(data.token.split('.')[1]))
      signIn(data.token, { id: payload.user_id, email: payload.email, role: payload.role })
      navigate('/wishlists')
    } catch (err) {
      setError(err.message)
    } finally {
      setLoading(false)
    }
  }

  async function handleRegister(e) {
    e.preventDefault()
    setError('')
    setLoading(true)
    try {
      const data = await register(name, email, password, age || null)
      const payload = JSON.parse(atob(data.token.split('.')[1]))
      signIn(data.token, { id: payload.user_id, email: payload.email, role: payload.role })
      navigate('/wishlists')
    } catch (err) {
      setError(err.message)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="page">
      <h1 style = {{ marginBottom: "15px" }}>Добро пожаловать!</h1>
      <div className="card">
        <div className="tabs">
          <button
            className={getTabClass('login')}
            onClick={() => { setTab('login'); setError('') }}
          >
            Вход
          </button>
          <button
            className={getTabClass('register')}
            onClick={() => { setTab('register'); setError('') }}
          >
            Регистрация
          </button>
        </div>

        {error && <div className="error">{error}</div>}

        {tab === 'login' ? (
          <form onSubmit={handleLogin}>
            <Field label="Email" type="email" value={email} onChange={setEmail} />
            <Field label="Пароль" type="password" value={password} onChange={setPassword} />
            <button className="btn-submit" disabled={loading}>
              {loading ? 'Входим...' : 'Войти'}
            </button>
          </form>
        ) : (
          <form onSubmit={handleRegister}>
            <Field label="Имя" type="text" value={name} onChange={setName} />
            <Field label="Email" type="email" value={email} onChange={setEmail} />
            <Field label="Пароль" type="password" value={password} onChange={setPassword} />
            <Field label="Возраст (необязательно)" type="number" value={age} onChange={setAge} />
            <button className="btn-submit" disabled={loading}>
              {loading ? 'Регистрируем...' : 'Зарегистрироваться'}
            </button>
          </form>
        )}
      </div>
    </div>
  )
}

function Field({ label, type, value, onChange }) {
  return (
    <div className="form-group">
      <label className="label">{label}</label>
      <input
        className="input"
        type={type}
        value={value}
        onChange={e => onChange(e.target.value)}
        required={!label.includes('необязательно')}
      />
    </div>
  )
}