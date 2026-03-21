import logo from '../../assets/logo.svg';
import CartIcon from '../Icons/CartIcon';
import type { NavbarProps } from '../../types'
import { Link, useLocation } from 'react-router-dom';
import { useCart } from '../CartOverlay/context/CartContext';
import './Navbar.css';

const Navbar: React.FC<NavbarProps> = ({ categories }) => {
    const location = useLocation();
    const { totalItems, dispatch } = useCart();

    return (
        <nav className="navbar">
            <div className="navbar__container">
                <div className="navbar__left">
                    {categories.map((category) => {
                        const isActive = location.pathname === `/${category}` || (location.pathname === '/' && category === categories[0]);
                        return (
                            <Link
                                key={category}
                                to={`/${category}`}
                                className={`navbar__item ${isActive ? 'active' : ''}`}
                                data-testid={isActive ? 'active-category-link' : 'category-link'}>
                                <span className="navbar__itemText">{category}</span>
                            </Link>
                        )
                    })}
                </div>

                <div className="navbar__logo">
                    <img src={logo} alt="Logo" className="logo__img" />
                </div>

                <div className="navbar__right">
                    <button
                        className="cart__btn"
                        data-testid='cart-btn'
                        aria-label='Open Cart'
                        onClick={() => dispatch({ type: 'TOGGLE_CART_OVERLAY' })}
                    >
                        <CartIcon className="cart__icon" size={20} />
                        {totalItems > 0 && (
                            <span className="cart__badge" data-testid="cart-badge">{totalItems}</span>
                        )}
                    </button>
                </div>
            </div>
        </nav>
    );
};

export default Navbar;
