'use client'

import { AccountsTable } from '@/components/modules/AccountsTable'
import { CreditCard, TrendingUp, AlertCircle } from 'lucide-react'

export default function ComptesPage() {
  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      {/* Page Header */}
      <div>
        <h1 className="text-3xl font-bold text-foreground mb-2">Accounts</h1>
        <p className="text-muted-foreground">Manage all your bank accounts in one place</p>
      </div>

      {/* Summary Cards */}
      <div className="grid md:grid-cols-3 gap-6">
        {/* Total Balance */}
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Total Balance</h3>
            <div className="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
              <CreditCard className="w-5 h-5 text-primary" />
            </div>
          </div>
          <p className="text-3xl font-bold text-foreground">$69,413.25</p>
          <p className="text-sm text-muted-foreground mt-2">Across all accounts</p>
        </div>

        {/* Active Accounts */}
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Active Accounts</h3>
            <div className="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <TrendingUp className="w-5 h-5 text-green-600" />
            </div>
          </div>
          <p className="text-3xl font-bold text-foreground">3</p>
          <p className="text-sm text-muted-foreground mt-2">All accounts active</p>
        </div>

        {/* Monthly Income */}
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">This Month</h3>
            <div className="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
              <AlertCircle className="w-5 h-5 text-blue-600" />
            </div>
          </div>
          <p className="text-3xl font-bold text-foreground">$3,500.00</p>
          <p className="text-sm text-green-600 mt-2">+12% from last month</p>
        </div>
      </div>

      {/* Accounts Table */}
      <AccountsTable />

      {/* Additional Info */}
      <div className="grid md:grid-cols-2 gap-6">
        {/* Tips */}
        <div className="bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-200 dark:border-blue-900/50 p-6">
          <h3 className="font-semibold text-foreground mb-3">💡 Account Tips</h3>
          <ul className="space-y-2 text-sm text-muted-foreground">
            <li className="flex gap-2">
              <span className="text-primary">•</span>
              <span>You can hold up to 10 accounts</span>
            </li>
            <li className="flex gap-2">
              <span className="text-primary">•</span>
              <span>Freeze accounts to prevent access</span>
            </li>
            <li className="flex gap-2">
              <span className="text-primary">•</span>
              <span>Each account can have its own settings</span>
            </li>
          </ul>
        </div>

        {/* Security Info */}
        <div className="bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-200 dark:border-green-900/50 p-6">
          <h3 className="font-semibold text-foreground mb-3">🔒 Security Information</h3>
          <ul className="space-y-2 text-sm text-muted-foreground">
            <li className="flex gap-2">
              <span className="text-green-600">✓</span>
              <span>All accounts are FDIC insured</span>
            </li>
            <li className="flex gap-2">
              <span className="text-green-600">✓</span>
              <span>256-bit encryption for all transactions</span>
            </li>
            <li className="flex gap-2">
              <span className="text-green-600">✓</span>
              <span>Multi-factor authentication enabled</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  )
}
