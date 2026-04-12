'use client'

import Link from 'next/link'
import { usePathname } from 'next/navigation'
import {
  Home,
  CreditCard,
  Banknote,
  Camera,
  ArrowRightLeft,
  AlertCircle,
  Settings,
  TrendingUp,
  LogOut,
  Menu,
  X,
  Shield,
  Wallet,
} from 'lucide-react'
import { useState } from 'react'

const navigationItems = [
  { name: 'Dashboard', href: '/', icon: Home, badge: null },
  { name: 'Comptes', href: '/comptes', icon: CreditCard, badge: null },
  { name: 'Transactions', href: '/transactions', icon: ArrowRightLeft, badge: null },
  { name: 'Chèques Numériques', href: '/cheques', icon: Banknote, badge: null },
  { name: 'Scanner de Chèque', href: '/scanner', icon: Camera, badge: 'NEW' },
  { name: 'Crédit Simulation', href: '/credit-simulation', icon: TrendingUp, badge: null },
  { name: 'Réclamations', href: '/reclamations', icon: AlertCircle, badge: null },
  { name: 'Paramètres', href: '/settings', icon: Settings, badge: null },
]

const adminItems = [
  { name: 'Admin Panel', href: '/admin', icon: Shield, badge: null },
]

export function Sidebar() {
  const pathname = usePathname()
  const [isMobileOpen, setIsMobileOpen] = useState(false)

  const isActive = (href: string) => {
    if (href === '/') {
      return pathname === '/'
    }
    return pathname.startsWith(href)
  }

  return (
    <>
      {/* Mobile Menu Button */}
      <button
        onClick={() => setIsMobileOpen(!isMobileOpen)}
        className="fixed bottom-6 right-6 z-40 md:hidden w-14 h-14 bg-gradient-to-r from-primary to-secondary text-white rounded-full flex items-center justify-center shadow-lg shadow-primary/30 transition-all duration-300 ease-in-out hover:scale-110 active:scale-95"
      >
        {isMobileOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
      </button>

      {/* Mobile Overlay */}
      {isMobileOpen && (
        <div
          className="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 md:hidden transition-opacity"
          onClick={() => setIsMobileOpen(false)}
        />
      )}

      {/* Sidebar */}
      <aside
        className={`fixed left-0 top-0 z-40 h-screen w-64 bg-sidebar border-r border-sidebar-border transition-transform duration-300 md:translate-x-0 flex flex-col pt-16 ${
          isMobileOpen ? 'translate-x-0' : '-translate-x-full'
        }`}
      >
        {/* Logo */}
        <div className="px-6 py-5 border-b border-sidebar-border">
          <Link href="/" className="flex items-center gap-3 group">
            <div className="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-primary/20">
              <Wallet className="w-5 h-5 text-white" />
            </div>
            <div>
              <span className="font-bold text-sidebar-foreground text-lg tracking-tight">ESPRIT</span>
              <span className="text-xs text-muted-foreground block -mt-0.5">WALLET</span>
            </div>
          </Link>
        </div>

        {/* Navigation */}
        <nav className="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
          <p className="px-3 mb-2 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Menu Principal</p>
          {navigationItems.map((item) => {
            const Icon = item.icon
            const active = isActive(item.href)
            return (
              <Link
                key={item.href}
                href={item.href}
                onClick={() => setIsMobileOpen(false)}
                className={`relative flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 ease-out text-sm font-medium group ${
                  active
                    ? 'bg-gradient-to-r from-primary to-primary/90 text-white shadow-md shadow-primary/20'
                    : 'text-sidebar-foreground hover:bg-muted/60'
                }`}
              >
                <Icon className={`w-[18px] h-[18px] flex-shrink-0 transition-transform group-hover:scale-110 ${active ? 'text-white' : ''}`} />
                <span className="flex-1 truncate">{item.name}</span>
                {item.badge && (
                  <span className="px-1.5 py-0.5 text-[9px] font-bold rounded-md bg-gradient-to-r from-cyan-400 to-blue-500 text-white uppercase tracking-wider animate-pulse">
                    {item.badge}
                  </span>
                )}
                {active && (
                  <div className="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-r-full" />
                )}
              </Link>
            )
          })}

          {/* Admin Section */}
          <div className="pt-4 mt-4 border-t border-sidebar-border">
            <p className="px-3 mb-2 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Administration</p>
            {adminItems.map((item) => {
              const Icon = item.icon
              const active = isActive(item.href)
              return (
                <Link
                  key={item.href}
                  href={item.href}
                  onClick={() => setIsMobileOpen(false)}
                  className={`relative flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 ease-out text-sm font-medium group ${
                    active
                      ? 'bg-gradient-to-r from-primary to-primary/90 text-white shadow-md shadow-primary/20'
                      : 'text-sidebar-foreground hover:bg-muted/60'
                  }`}
                >
                  <Icon className={`w-[18px] h-[18px] flex-shrink-0 transition-transform group-hover:scale-110 ${active ? 'text-white' : ''}`} />
                  <span className="flex-1 truncate">{item.name}</span>
                </Link>
              )
            })}
          </div>
        </nav>

        {/* Bottom Section */}
        <div className="border-t border-sidebar-border p-3">
          {/* User summary mini card */}
          <div className="flex items-center gap-3 px-3 py-2.5 mb-2 bg-muted/40 rounded-xl">
            <div className="w-8 h-8 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
              JD
            </div>
            <div className="min-w-0 flex-1">
              <p className="text-xs font-semibold text-foreground truncate">John Doe</p>
              <p className="text-[10px] text-muted-foreground truncate">john@esprit.tn</p>
            </div>
          </div>
          <Link href="/auth/login" className="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
            <LogOut className="w-[18px] h-[18px]" />
            <span>Déconnexion</span>
          </Link>
        </div>
      </aside>
    </>
  )
}
