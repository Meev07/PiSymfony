'use client'

import { useState } from 'react'
import {
  ArrowUpRight,
  ArrowDownLeft,
  Download,
  Filter,
  Search,
} from 'lucide-react'

interface Transaction {
  id: number
  type: 'sent' | 'received'
  description: string
  amount: number
  date: string
  time: string
  status: 'completed' | 'pending' | 'failed'
  category: string
  reference: string
}

const mockTransactions: Transaction[] = [
  {
    id: 1,
    type: 'sent',
    description: 'Transfer to Sarah Anderson',
    amount: 500.00,
    date: '2024-04-04',
    time: '2:30 PM',
    status: 'completed',
    category: 'Transfer',
    reference: 'TXN001',
  },
  {
    id: 2,
    type: 'received',
    description: 'Salary Deposit - Acme Corp',
    amount: 5_000.00,
    date: '2024-04-02',
    time: '9:00 AM',
    status: 'completed',
    category: 'Income',
    reference: 'DEP001',
  },
  {
    id: 3,
    type: 'sent',
    description: 'Bill Payment - Electric Company',
    amount: 150.00,
    date: '2024-04-01',
    time: '11:45 AM',
    status: 'completed',
    category: 'Bills',
    reference: 'BIL001',
  },
  {
    id: 4,
    type: 'received',
    description: 'Freelance Project - Web Design',
    amount: 1_200.00,
    date: '2024-03-31',
    time: '4:15 PM',
    status: 'completed',
    category: 'Income',
    reference: 'PRJ001',
  },
  {
    id: 5,
    type: 'sent',
    description: 'Restaurant - Downtown Bistro',
    amount: 85.50,
    date: '2024-03-30',
    time: '8:30 PM',
    status: 'completed',
    category: 'Dining',
    reference: 'POS001',
  },
  {
    id: 6,
    type: 'sent',
    description: 'Transfer to Savings Account',
    amount: 2_000.00,
    date: '2024-03-28',
    time: '10:00 AM',
    status: 'pending',
    category: 'Transfer',
    reference: 'TXN002',
  },
]

export default function TransactionsPage() {
  const [transactions, setTransactions] = useState<Transaction[]>(mockTransactions)
  const [searchQuery, setSearchQuery] = useState('')
  const [selectedFilter, setSelectedFilter] = useState<'all' | 'sent' | 'received'>('all')
  const [sortBy, setSortBy] = useState<'recent' | 'amount'>('recent')

  const filteredTransactions = transactions
    .filter(t => {
      if (selectedFilter !== 'all' && t.type !== selectedFilter) return false
      if (searchQuery && !t.description.toLowerCase().includes(searchQuery.toLowerCase())) {
        return false
      }
      return true
    })
    .sort((a, b) => {
      if (sortBy === 'recent') {
        return new Date(b.date).getTime() - new Date(a.date).getTime()
      } else {
        return b.amount - a.amount
      }
    })

  const totalSent = transactions
    .filter(t => t.type === 'sent')
    .reduce((sum, t) => sum + t.amount, 0)
  
  const totalReceived = transactions
    .filter(t => t.type === 'received')
    .reduce((sum, t) => sum + t.amount, 0)

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'completed':
        return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
      case 'pending':
        return 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400'
      case 'failed':
        return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
      default:
        return 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-400'
    }
  }

  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      {/* Page Header */}
      <div>
        <h1 className="text-3xl font-bold text-foreground mb-2">Transactions</h1>
        <p className="text-muted-foreground">View and manage all your transactions</p>
      </div>

      {/* Summary Cards */}
      <div className="grid md:grid-cols-3 gap-6">
        {/* Sent */}
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Total Sent</h3>
            <div className="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
              <ArrowUpRight className="w-5 h-5 text-red-600" />
            </div>
          </div>
          <p className="text-3xl font-bold text-foreground">
            ${totalSent.toLocaleString('en-US', { minimumFractionDigits: 2 })}
          </p>
          <p className="text-sm text-muted-foreground mt-2">
            {transactions.filter(t => t.type === 'sent').length} transactions
          </p>
        </div>

        {/* Received */}
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Total Received</h3>
            <div className="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <ArrowDownLeft className="w-5 h-5 text-green-600" />
            </div>
          </div>
          <p className="text-3xl font-bold text-foreground">
            ${totalReceived.toLocaleString('en-US', { minimumFractionDigits: 2 })}
          </p>
          <p className="text-sm text-muted-foreground mt-2">
            {transactions.filter(t => t.type === 'received').length} transactions
          </p>
        </div>

        {/* Net */}
        <div className="bg-gradient-to-br from-primary/10 to-secondary/10 rounded-2xl border border-primary/20 shadow-md shadow-black/5 p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Net Change</h3>
            <div className="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center">
              <ArrowDownLeft className="w-5 h-5 text-primary" />
            </div>
          </div>
          <p className="text-3xl font-bold text-primary">
            ${(totalReceived - totalSent).toLocaleString('en-US', { minimumFractionDigits: 2 })}
          </p>
          <p className="text-sm text-muted-foreground mt-2">This month</p>
        </div>
      </div>

      {/* Filters & Search */}
      <div className="space-y-4">
        {/* Search & Actions */}
        <div className="flex flex-col md:flex-row gap-3">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none" />
            <input
              type="text"
              placeholder="Search transactions..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full pl-10 pr-4 py-2.5 bg-input border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all duration-300 ease-in-out"
            />
          </div>
          <button className="flex items-center gap-2 px-4 py-2.5 bg-card border border-border text-foreground rounded-lg hover:bg-muted transition-all duration-300 ease-in-out">
            <Download className="w-4 h-4" />
            Export
          </button>
        </div>

        {/* Filter Tabs */}
        <div className="flex gap-2 flex-wrap">
          {[
            { id: 'all', label: 'All Transactions' },
            { id: 'received', label: 'Received' },
            { id: 'sent', label: 'Sent' },
          ].map((filter) => (
            <button
              key={filter.id}
              onClick={() => setSelectedFilter(filter.id as any)}
              className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
                selectedFilter === filter.id
                  ? 'bg-primary text-primary-foreground'
                  : 'bg-muted text-foreground hover:bg-muted/80'
              }`}
            >
              {filter.label}
            </button>
          ))}
        </div>

        {/* Sort */}
        <div className="flex items-center gap-2">
          <Filter className="w-4 h-4 text-muted-foreground" />
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value as any)}
            className="px-3 py-1.5 bg-input border border-border rounded text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
          >
            <option value="recent">Most Recent</option>
            <option value="amount">Highest Amount</option>
          </select>
        </div>
      </div>

      {/* Transactions List */}
      <div className="space-y-2">
        {filteredTransactions.length > 0 ? (
          filteredTransactions.map((transaction) => (
            <div
              key={transaction.id}
              className="bg-card rounded-lg border border-border shadow-md shadow-black/5 p-4 hover:border-primary/50 hover:shadow-md transition-all cursor-pointer"
            >
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-4 flex-1">
                  <div className={`w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 ${
                    transaction.type === 'sent'
                      ? 'bg-red-100 dark:bg-red-900/30'
                      : 'bg-green-100 dark:bg-green-900/30'
                  }`}>
                    {transaction.type === 'sent' ? (
                      <ArrowUpRight className={`w-6 h-6 ${
                        transaction.type === 'sent' ? 'text-red-600' : 'text-green-600'
                      }`} />
                    ) : (
                      <ArrowDownLeft className="w-6 h-6 text-green-600" />
                    )}
                  </div>

                  <div className="flex-1">
                    <p className="font-medium text-foreground">{transaction.description}</p>
                    <div className="flex items-center gap-3 mt-1 flex-wrap">
                      <p className="text-xs text-muted-foreground">
                        {new Date(transaction.date).toLocaleDateString()} at {transaction.time}
                      </p>
                      <span className="px-2 py-0.5 bg-muted rounded text-xs text-muted-foreground">
                        {transaction.category}
                      </span>
                      <span className={`px-2 py-0.5 rounded text-xs font-medium ${getStatusColor(transaction.status)}`}>
                        {transaction.status.charAt(0).toUpperCase() + transaction.status.slice(1)}
                      </span>
                    </div>
                  </div>
                </div>

                <div className="text-right ml-4">
                  <p className={`text-lg font-bold ${
                    transaction.type === 'sent' ? 'text-foreground' : 'text-green-600'
                  }`}>
                    {transaction.type === 'sent' ? '-' : '+'} ${transaction.amount.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </p>
                  <p className="text-xs text-muted-foreground mt-1">{transaction.reference}</p>
                </div>
              </div>
            </div>
          ))
        ) : (
          <div className="text-center py-12 bg-card rounded-2xl border border-border border-dashed">
            <p className="text-muted-foreground mb-2">No transactions found</p>
            <p className="text-sm text-muted-foreground">Try adjusting your filters or search</p>
          </div>
        )}
      </div>
    </div>
  )
}
