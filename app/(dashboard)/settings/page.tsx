'use client'

import { useState } from 'react'
import {
  User,
  Lock,
  Bell,
  Shield,
  Smartphone,
  LogOut,
  Eye,
  EyeOff,
  Check,
  X,
} from 'lucide-react'

export default function SettingsPage() {
  const [activeTab, setActiveTab] = useState('profile')
  const [showPassword, setShowPassword] = useState(false)
  const [isSaving, setIsSaving] = useState(false)

  const tabs = [
    { id: 'profile', label: 'Profile', icon: User },
    { id: 'security', label: 'Security', icon: Shield },
    { id: 'notifications', label: 'Notifications', icon: Bell },
  ]

  const handleSave = async () => {
    setIsSaving(true)
    setTimeout(() => setIsSaving(false), 1000)
  }

  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      {/* Page Header */}
      <div>
        <h1 className="text-3xl font-bold text-foreground mb-2">Settings</h1>
        <p className="text-muted-foreground">Manage your account preferences and security</p>
      </div>

      {/* Tabs */}
      <div className="flex gap-2 border-b border-border overflow-x-auto">
        {tabs.map((tab) => {
          const Icon = tab.icon
          return (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              className={`flex items-center gap-2 px-4 py-3 border-b-2 transition-colors whitespace-nowrap ${
                activeTab === tab.id
                  ? 'border-primary text-primary font-medium'
                  : 'border-transparent text-muted-foreground hover:text-foreground'
              }`}
            >
              <Icon className="w-4 h-4" />
              {tab.label}
            </button>
          )
        })}
      </div>

      {/* Profile Tab */}
      {activeTab === 'profile' && (
        <div className="space-y-6">
          {/* Profile Picture */}
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
            <h3 className="text-lg font-semibold text-foreground mb-4">Profile Picture</h3>
            <div className="flex items-center gap-6">
              <div className="w-20 h-20 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center text-white text-3xl font-bold">
                JD
              </div>
              <div className="space-y-2">
                <button className="px-4 py-2 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:opacity-90 transition-all duration-300 ease-in-out">
                  Upload Photo
                </button>
                <button className="block text-sm text-red-600 hover:underline">
                  Remove
                </button>
              </div>
            </div>
          </div>

          {/* Personal Information */}
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6 space-y-4">
            <h3 className="text-lg font-semibold text-foreground mb-4">Personal Information</h3>

            <div className="grid md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-foreground mb-2">First Name</label>
                <input
                  type="text"
                  defaultValue="John"
                  className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-foreground mb-2">Last Name</label>
                <input
                  type="text"
                  defaultValue="Doe"
                  className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
                />
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Email Address</label>
              <input
                type="email"
                defaultValue="john@example.com"
                className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Phone Number</label>
              <input
                type="tel"
                defaultValue="+1 (555) 123-4567"
                className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Date of Birth</label>
              <input
                type="date"
                defaultValue="1990-01-15"
                className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
              />
            </div>

            <button
              onClick={handleSave}
              disabled={isSaving}
              className="px-6 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:opacity-90 transition-all duration-300 ease-in-out disabled:opacity-50"
            >
              {isSaving ? 'Saving...' : 'Save Changes'}
            </button>
          </div>
        </div>
      )}

      {/* Security Tab */}
      {activeTab === 'security' && (
        <div className="space-y-6">
          {/* Change Password */}
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6 space-y-4">
            <h3 className="text-lg font-semibold text-foreground mb-4">Change Password</h3>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Current Password</label>
              <div className="relative">
                <input
                  type={showPassword ? 'text' : 'password'}
                  placeholder="••••••••"
                  className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
                />
                <button className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                  {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">New Password</label>
              <input
                type="password"
                placeholder="••••••••"
                className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Confirm Password</label>
              <input
                type="password"
                placeholder="••••••••"
                className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
              />
            </div>

            <button className="px-6 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:opacity-90 transition-all duration-300 ease-in-out">
              Update Password
            </button>
          </div>

          {/* Two-Factor Authentication */}
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
            <div className="flex items-center justify-between mb-4">
              <div>
                <h3 className="text-lg font-semibold text-foreground">Two-Factor Authentication</h3>
                <p className="text-sm text-muted-foreground mt-1">Add an extra layer of security</p>
              </div>
              <label className="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" defaultChecked className="w-5 h-5 rounded" />
                <span className="text-sm font-medium text-foreground">Enabled</span>
              </label>
            </div>
            <p className="text-sm text-muted-foreground">
              Use an authenticator app to generate one-time codes for login
            </p>
            <button className="mt-4 px-4 py-2 border border-border text-foreground rounded-lg text-sm font-medium hover:bg-muted transition-all duration-300 ease-in-out">
              Manage 2FA
            </button>
          </div>

          {/* Active Devices */}
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
            <h3 className="text-lg font-semibold text-foreground mb-4">Active Devices</h3>
            <div className="space-y-3">
              {[
                { name: 'MacBook Pro', browser: 'Safari', ip: '192.168.1.1', current: true },
                { name: 'iPhone 13', browser: 'Safari', ip: '192.168.1.2', current: false },
                { name: 'Windows PC', browser: 'Chrome', ip: '192.168.1.3', current: false },
              ].map((device, idx) => (
                <div key={idx} className="flex items-center justify-between p-3 bg-muted/50 rounded-lg">
                  <div>
                    <p className="font-medium text-foreground text-sm">
                      {device.name} {device.current && '(Current)'}
                    </p>
                    <p className="text-xs text-muted-foreground">
                      {device.browser} • {device.ip}
                    </p>
                  </div>
                  {!device.current && (
                    <button className="text-xs text-red-600 hover:underline font-medium">
                      Remove
                    </button>
                  )}
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* Notifications Tab */}
      {activeTab === 'notifications' && (
        <div className="space-y-6">
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6 space-y-4">
            <h3 className="text-lg font-semibold text-foreground mb-4">Email Notifications</h3>

            {[
              { title: 'Transaction alerts', desc: 'Get notified for all transactions' },
              { title: 'Security alerts', desc: 'Login attempts and security changes' },
              { title: 'Cheque updates', desc: 'Status updates on your cheques' },
              { title: 'Account changes', desc: 'Changes to your account settings' },
              { title: 'Marketing emails', desc: 'News, tips, and special offers' },
              { title: 'Monthly statements', desc: 'Monthly account summaries' },
            ].map((notif, idx) => (
              <div key={idx} className="flex items-center justify-between p-3 border border-border rounded-lg">
                <div>
                  <p className="font-medium text-foreground text-sm">{notif.title}</p>
                  <p className="text-xs text-muted-foreground">{notif.desc}</p>
                </div>
                <label className="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" defaultChecked className="sr-only peer" />
                  <div className="w-11 h-6 bg-border peer-checked:bg-primary rounded-full peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all" />
                </label>
              </div>
            ))}

            <button
              onClick={handleSave}
              disabled={isSaving}
              className="px-6 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:opacity-90 transition-all duration-300 ease-in-out disabled:opacity-50 mt-4"
            >
              {isSaving ? 'Saving...' : 'Save Preferences'}
            </button>
          </div>
        </div>
      )}

      {/* Danger Zone */}
      <div className="bg-red-50 dark:bg-red-900/20 rounded-2xl border border-red-200 dark:border-red-900/50 p-6">
        <h3 className="text-lg font-bold text-red-600 dark:text-red-400 mb-4">Danger Zone</h3>
        <div className="space-y-3">
          <button className="w-full px-4 py-3 border-2 border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-400 rounded-lg font-medium hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors text-left flex items-center justify-between group">
            <span>Sign Out Everywhere</span>
            <LogOut className="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
          </button>
          <button className="w-full px-4 py-3 border-2 border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-400 rounded-lg font-medium hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors text-left flex items-center justify-between group">
            <span>Delete Account</span>
            <X className="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
          </button>
        </div>
      </div>
    </div>
  )
}
