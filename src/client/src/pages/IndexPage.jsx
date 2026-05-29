import { Link } from 'react-router-dom'
import { useAuth } from '../AuthContext'
import './css/IndexPage.css'
import logo from '../../images/logo.png'


export default function IndexPage() {
  const { user } = useAuth()

  return (
    <div>
      <nav className="nav">
        <Link to="/" className="nav-logo">Wishlist</Link>
        <div className="nav-links">
        {user ? (
          <>
            <Link to="/wishlists">Вишлист</Link>
            <Link to="/friends">Друзья</Link>
            <Link to="/profile">Личный кабинет</Link>
          </>
        ) : (
          <Link to="/login">Войти</Link>
        )}
        </div>
      </nav>

      <main className="hero">
        <img src={logo} alt="Wishlist" className="hero-logo" />
        <h1>Добро пожаловать в Wishlist!</h1>
        <p>Создавай списки желаний и делись ими с друзьями.</p>
        {!user && <Link to="/login" className="hero-btn">Войти или зарегистрироваться</Link>}
      </main>
    </div>
  )
}