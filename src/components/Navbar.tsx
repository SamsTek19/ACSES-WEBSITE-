import React, { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { Menu, X, ChevronRight } from 'lucide-react';

export const Navbar: React.FC = () => {
  const [isOpen, setIsOpen] = useState(false);
  const location = useLocation();
  const isHome = location.pathname === '/';

  const navLinks = [
    { name: 'Home', path: '/' },
    { name: 'Events', path: '/events' },
    { name: 'News', path: '/news' },
    { name: 'Clubs', path: '/clubs' },
    { name: 'Resources', path: '/resources' },
  ];

  const isActive = (path: string) => location.pathname === path;

  return (
    <header className={isHome
      ? 'absolute inset-x-0 top-0 z-50 border-b border-white/10 bg-transparent text-white'
      : 'sticky top-0 z-50 border-b border-emerald-100 bg-white/95 text-slate-900 shadow-sm backdrop-blur-md'}>
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-20 items-center">
          
          {/* Logo */}
          <Link to="/" className="flex items-center space-x-3 group">
            <div className="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-white shadow-md shadow-emerald-800/20 transition-transform group-hover:scale-105">
              <img src="/acses-logo.png" alt="ACSES logo" className="h-full w-full object-contain" />
            </div>
            <div>
              <span className={`text-xl font-bold tracking-tight block leading-none ${isHome ? 'text-white' : 'text-slate-900'}`}>
                ACSES-UMaT
              </span>
              <span className={`text-xs font-medium tracking-wider uppercase mt-1 block ${isHome ? 'text-white/75' : 'text-slate-500'}`}>
                Association of Computer Science & Engineering Students
              </span>
            </div>
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex items-center space-x-1">
            {navLinks.map((link) => (
              <Link
                key={link.name}
                to={link.path}
                className={`px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 ${
                  isHome
                    ? isActive(link.path)
                      ? 'bg-white/15 text-white font-bold'
                      : 'text-white/90 hover:text-white hover:bg-white/10'
                    : isActive(link.path)
                      ? 'text-emerald-800 bg-emerald-50 font-bold'
                      : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50'
                }`}
              >
                {link.name}
              </Link>
            ))}
            <Link
              to="/executives"
              className="ml-4 inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white bg-emerald-800 hover:bg-emerald-700 transition-colors shadow-sm"
            >
              Executives
              <ChevronRight className="w-4 h-4 ml-1" />
            </Link>
          </nav>

          {/* Mobile Hamburger Button */}
          <div className="flex md:hidden">
            <button
              onClick={() => setIsOpen(!isOpen)}
              type="button"
              className={`p-2 rounded-lg focus:outline-none ${isHome ? 'text-white hover:bg-white/10' : 'text-slate-600 hover:text-emerald-700 hover:bg-emerald-50'}`}
              aria-label="Toggle Menu"
            >
              {isOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
            </button>
          </div>
        </div>
      </div>

      {/* Mobile Menu Dropdown */}
      {isOpen && (
        <div className={`md:hidden border-b ${isHome ? 'border-white/10 bg-slate-950/95 text-white backdrop-blur-md' : 'border-emerald-100 bg-white'}`}>
          <div className="px-4 pt-2 pb-6 space-y-2">
            {navLinks.map((link) => (
              <Link
                key={link.name}
                to={link.path}
                onClick={() => setIsOpen(false)}
                className={`block px-4 py-3 rounded-lg text-base font-medium ${
                  isHome
                    ? isActive(link.path)
                      ? 'text-white bg-white/15 font-semibold'
                      : 'text-white/90 hover:text-white hover:bg-white/10'
                    : isActive(link.path)
                      ? 'text-emerald-800 bg-emerald-50 font-semibold'
                      : 'text-slate-600 hover:text-emerald-700 hover:bg-slate-50'
                }`}
              >
                {link.name}
              </Link>
            ))}
            <Link
              to="/executives"
              onClick={() => setIsOpen(false)}
              className="block w-full text-center mt-4 px-4 py-3 rounded-lg text-base font-semibold text-white bg-emerald-800 hover:bg-emerald-700"
            >
              Know Your Executives
            </Link>
          </div>
        </div>
      )}
    </header>
  );
};