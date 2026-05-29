import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider, useAuth } from './AuthContext'
import LoginPage from './pages/LoginPage'
import IndexPage from './pages/IndexPage'
import WishlistsPage from './pages/WishlistsPage'
import ProfilePage from './pages/ProfilePage'
import CreateWishlist from './pages/CreateWishlist'
import WhishlistPage from './pages/WhishlistPage'
import CreateWishlistItemPage from './pages/CreateWishlistItemPage'
import FriendsPage from './pages/FriendsPage'
import FriendWishlistsPage from './pages/FriendWishlistsPage'
import FriendWishlistPage from './pages/FriendWishlistPage'

function PrivateRoute({ children }) {
  const { token } = useAuth()
  return token ? children : <Navigate to="/login" replace />
}

export default function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<IndexPage />} />
          <Route path="/login" element={<LoginPage />} />
          <Route
            path="/wishlists"
            element={<PrivateRoute><WishlistsPage /></PrivateRoute>}
          />
          <Route
            path="/create-wishlist"
            element={<PrivateRoute><CreateWishlist /></PrivateRoute>}
          />
          <Route
            path="/profile"
            element={<PrivateRoute><ProfilePage /></PrivateRoute>}
          />
          <Route
            path="/friends"
            element={<PrivateRoute><FriendsPage /></PrivateRoute>}
          />
          <Route
            path="/friends/:userId/wishlists"
            element={<PrivateRoute><FriendWishlistsPage /></PrivateRoute>}
          />
          <Route
            path="/friends/:userId/wishlists/:wishlistId"
            element={<PrivateRoute><FriendWishlistPage /></PrivateRoute>}
          />
          <Route
            path="/wishlists/:id"
            element={<PrivateRoute><WhishlistPage /></PrivateRoute>}
          />
          <Route
            path="/wishlists/:id/add-item"
            element={<PrivateRoute><CreateWishlistItemPage /></PrivateRoute>}
          />
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </BrowserRouter>
    </AuthProvider>
  )
}
