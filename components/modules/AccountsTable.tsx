'use client'

import { useState } from 'react'
import {
  MoreVertical,
  Edit2,
  Trash2,
  Lock,
  Unlock,
  Plus,
} from 'lucide-react'

interface Account {
  id: number
  accountNumber: string
  type: 'Checking' | 'Savings' | 'Money Market'
  balance: number
  status: 'Active' | 'Frozen' | 'Closed'
  createdAt: string
}

const mockAccounts: Account[] = [
  {
    id: 1,
    accountNumber: '****2341',
    type: 'Checking',
    balance: 15_234.50,
    status: 'Active',
    createdAt: '2023-01-15',
  },
  {
    id: 2,
    accountNumber: '****7653',
    type: 'Savings',
    balance: 45_678.75,
    status: 'Active',
    createdAt: '2023-03-20',
  },
  {
    id: 3,
    accountNumber: '****4892',
    type: 'Money Market',
    balance: 8_500.00,
    status: 'Active',
    createdAt: '2023-06-10',
  },
]

export function AccountsTable() {
  const [accounts, setAccounts] = useState<Account[]>(mockAccounts)
  const [selectedAccount, setSelectedAccount] = useState<number | null>(null)
  const [showActions, setShowActions] = useState<number | null>(null)

  const toggleAccountStatus = (id: number) => {
    setAccounts(accounts.map(acc => 
      acc.id === id 
        ? { ...acc, status: acc.status === 'Active' ? 'Frozen' : 'Active' }
        : acc
    ))
  }

  const deleteAccount = (id: number) => {
    setAccounts(accounts.filter(acc => acc.id !== id))
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'Active':
        return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
      case 'Frozen':
        return 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
      case 'Closed':
        return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
      default:
        return 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-400'
    }
  }

  return (
    <div className="space-y-4">
      {/* Header */}
      <div className="flex items-center justify-between">
        <h2 className="text-2xl font-bold text-foreground">Your Accounts</h2>
        <button className="flex items-center gap-2 px-4 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:opacity-90 transition-all duration-300 ease-in-out active:scale-95">
          <Plus className="w-4 h-4" />
          Add Account
        </button>
      </div>

      {/* Table */}
      <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-border bg-muted/50">
                <th className="px-6 py-4 text-left text-sm font-semibold text-foreground">
                  Account Number
                </th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-foreground">
                  Type
                </th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-foreground">
                  Balance
                </th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-foreground">
                  Status
                </th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-foreground">
                  Created
                </th>
                <th className="px-6 py-4 text-right text-sm font-semibold text-foreground">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {accounts.map((account) => (
                <tr
                  key={account.id}
                  className={`hover:bg-muted/50 transition-colors cursor-pointer ${
                    selectedAccount === account.id ? 'bg-muted' : ''
                  }`}
                  onClick={() => setSelectedAccount(account.id)}
                >
                  <td className="px-6 py-4 text-sm font-medium text-foreground font-mono">
                    {account.accountNumber}
                  </td>
                  <td className="px-6 py-4 text-sm text-foreground">{account.type}</td>
                  <td className="px-6 py-4 text-sm font-semibold text-foreground">
                    ${account.balance.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </td>
                  <td className="px-6 py-4 text-sm">
                    <span className={`inline-block px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(account.status)}`}>
                      {account.status}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-sm text-muted-foreground">
                    {new Date(account.createdAt).toLocaleDateString()}
                  </td>
                  <td className="px-6 py-4 text-right">
                    <div className="relative">
                      <button
                        onClick={(e) => {
                          e.stopPropagation()
                          setShowActions(showActions === account.id ? null : account.id)
                        }}
                        className="p-2 hover:bg-muted rounded-lg transition-colors"
                      >
                        <MoreVertical className="w-4 h-4 text-muted-foreground" />
                      </button>

                      {showActions === account.id && (
                        <div className="absolute right-0 mt-2 w-48 bg-card border border-border rounded-lg shadow-lg overflow-hidden z-50">
                          <button className="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-foreground hover:bg-muted transition-colors border-b border-border">
                            <Edit2 className="w-4 h-4" />
                            Edit Account
                          </button>
                          <button
                            onClick={() => toggleAccountStatus(account.id)}
                            className="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-foreground hover:bg-muted transition-colors border-b border-border"
                          >
                            {account.status === 'Active' ? (
                              <>
                                <Lock className="w-4 h-4" />
                                Freeze Account
                              </>
                            ) : (
                              <>
                                <Unlock className="w-4 h-4" />
                                Unfreeze Account
                              </>
                            )}
                          </button>
                          <button
                            onClick={() => {
                              deleteAccount(account.id)
                              setShowActions(null)
                            }}
                            className="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                          >
                            <Trash2 className="w-4 h-4" />
                            Delete Account
                          </button>
                        </div>
                      )}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Empty State */}
      {accounts.length === 0 && (
        <div className="text-center py-12 bg-card rounded-2xl border border-border border-dashed">
          <h3 className="font-semibold text-foreground mb-2">No accounts yet</h3>
          <p className="text-sm text-muted-foreground mb-4">Create your first account to get started</p>
          <button className="px-4 py-2 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:opacity-90 transition-all duration-300 ease-in-out">
            Create Account
          </button>
        </div>
      )}
    </div>
  )
}
