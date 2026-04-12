'use client'

import { useState } from 'react'
import { Bell, ChevronDown, Search, Moon, Sun } from 'lucide-react'
import { NotificationDropdown } from './NotificationDropdown'
import { UserProfileDropdown } from './UserProfileDropdown'

export function TopBar() {
  const [showNotifications, setShowNotifications] = useState(false)
  const [showProfile, setShowProfile] = useState(false)
  const [searchQuery, setSearchQuery] = useState('')
  const [isDark, setIsDark] = useState(false)

  const toggleTheme = () => {
    setIsDark(!isDark)
    document.documentElement.classList.toggle('dark')
  }

  return (
    <header className="fixed top-0 left-0 right-0 z-30 bg-card/80 backdrop-blur-xl border-b border-border/50 h-16 md:pl-64">
      <div className="h-full px-4 md:px-6 flex items-center justify-between">
        {/* Search Bar */}
        <div className="hidden md:flex flex-1 max-w-lg">
          <div className="relative w-full group">
            <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
            <input
              type="text"
              placeholder="Rechercher transactions, comptes..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full pl-10 pr-4 py-2.5 bg-muted/50 border border-transparent rounded-xl text-sm placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/30 focus:bg-card focus:border-primary/20 transition-all duration-300 ease-in-out"
            />
          </div>
        </div>

        {/* Right Actions */}
        <div className="flex items-center gap-1.5 md:gap-3 ml-auto">
          {/* Search Button Mobile */}
          <button className="md:hidden p-2.5 hover:bg-muted rounded-xl transition-all duration-200">
            <Search className="w-5 h-5 text-foreground" />
          </button>

          {/* Theme Toggle */}
          <button
            onClick={toggleTheme}
            className="p-2.5 hover:bg-muted rounded-xl transition-all duration-200"
          >
            {isDark ? (
              <Sun className="w-5 h-5 text-yellow-500" />
            ) : (
              <Moon className="w-5 h-5 text-foreground" />
            )}
          </button>

          {/* Notifications */}
          <div className="relative">
            <button
              onClick={() => {
                setShowNotifications(!showNotifications)
                setShowProfile(false)
              }}
              className="relative p-2.5 hover:bg-muted rounded-xl transition-all duration-200"
            >
              <Bell className="w-5 h-5 text-foreground" />
              <span className="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full animate-pulse" />
              <span className="absolute top-1.5 right-1 min-w-[16px] h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center px-1">
                3
              </span>
            </button>
            {showNotifications && (
              <>
                <div
                  className="fixed inset-0"
                  onClick={() => setShowNotifications(false)}
                />
                <NotificationDropdown />
              </>
            )}
          </div>

          {/* Divider */}
          <div className="hidden sm:block w-px h-8 bg-border mx-1" />

          {/* User Profile */}
          <div className="relative">
            <button
              onClick={() => {
                setShowProfile(!showProfile)
                setShowNotifications(false)
              }}
              className="flex items-center gap-2.5 px-2 py-1.5 hover:bg-muted rounded-xl transition-all duration-200"
            >
              <div className="w-9 h-9 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center text-white text-sm font-bold shadow-sm">
                JD
              </div>
              <div className="hidden sm:block text-left">
                <p className="text-sm font-semibold text-foreground leading-tight">John Doe</p>
                <p className="text-[10px] text-muted-foreground">Utilisateur</p>
              </div>
              <ChevronDown className="w-4 h-4 text-muted-foreground hidden sm:block" />
            </button>
            {showProfile && (
              <>
                <div
                  className="fixed inset-0"
                  onClick={() => setShowProfile(false)}
                />
                <UserProfileDropdown />
              </>
            )}
          </div>
        </div>
      </div>
    </header>
  )
}
