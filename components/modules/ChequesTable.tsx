'use client'

import { useState } from 'react'
import {
  MoreVertical,
  Eye,
  Download,
  X,
  Plus,
  DollarSign,
} from 'lucide-react'

interface Cheque {
  id: number
  number: string
  sender: string
  receiver: string
  amount: number
  date: string
  status: 'Pending' | 'Confirmed' | 'Cancelled'
  bank: string
}

const mockCheques: Cheque[] = [
  {
    id: 1,
    number: 'CHQ001234',
    sender: 'Acme Corp',
    receiver: 'John Doe',
    amount: 5_000.00,
    date: '2024-04-01',
    status: 'Confirmed',
    bank: 'First National Bank',
  },
  {
    id: 2,
    number: 'CHQ001235',
    sender: 'John Doe',
    receiver: 'Tech Solutions Inc',
    amount: 1_500.00,
    date: '2024-03-28',
    status: 'Pending',
    bank: 'Global Finance',
  },
  {
    id: 3,
    number: 'CHQ001236',
    sender: 'Freelance Client',
    receiver: 'John Doe',
    amount: 2_500.00,
    date: '2024-03-25',
    status: 'Confirmed',
    bank: 'First National Bank',
  },
  {
    id: 4,
    number: 'CHQ001237',
    sender: 'John Doe',
    receiver: 'Utility Company',
    amount: 350.00,
    date: '2024-03-20',
    status: 'Cancelled',
    bank: 'City Bank',
  },
]

export function ChequesTable() {
  const [cheques, setCheques] = useState<Cheque[]>(mockCheques)
  const [selectedCheque, setSelectedCheque] = useState<number | null>(null)
  const [showActions, setShowActions] = useState<number | null>(null)

  const cancelCheque = (id: number) => {
    setCheques(cheques.map(cheque =>
      cheque.id === id
        ? { ...cheque, status: 'Cancelled' }
        : cheque
    ))
    setShowActions(null)
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'Confirmed':
        return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
      case 'Pending':
        return 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400'
      case 'Cancelled':
        return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
      default:
        return 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-400'
    }
  }

  return (
    <div className="space-y-4">
      {/* Header */}
      <div className="flex items-center justify-between">
        <h2 className="text-2xl font-bold text-foreground">Digital Cheques</h2>
        <button className="flex items-center gap-2 px-4 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:opacity-90 transition-all duration-300 ease-in-out active:scale-95">
          <Plus className="w-4 h-4" />
          Create Cheque
        </button>
      </div>

      {/* Filter Tabs */}
      <div className="flex gap-2 flex-wrap">
        {['All', 'Pending', 'Confirmed', 'Cancelled'].map((filter) => (
          <button
            key={filter}
            className="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-muted text-foreground hover:bg-primary hover:text-primary-foreground"
          >
            {filter}
          </button>
        ))}
      </div>

      {/* Table */}
      <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b border-border bg-muted/50">
                <th className="px-6 py-4 text-left text-sm font-semibold text-foreground">
                  Cheque #
                </th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-foreground">
                  From / To
                </th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-foreground">
                  Amount
                </th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-foreground">
                  Bank
                </th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-foreground">
                  Date
                </th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-foreground">
                  Status
                </th>
                <th className="px-6 py-4 text-right text-sm font-semibold text-foreground">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-border">
              {cheques.map((cheque) => (
                <tr
                  key={cheque.id}
                  className={`hover:bg-muted/50 transition-colors cursor-pointer ${
                    selectedCheque === cheque.id ? 'bg-muted' : ''
                  }`}
                  onClick={() => setSelectedCheque(cheque.id)}
                >
                  <td className="px-6 py-4 text-sm font-mono font-semibold text-foreground">
                    {cheque.number}
                  </td>
                  <td className="px-6 py-4 text-sm text-foreground">
                    <div>
                      <p className="font-medium">{cheque.sender}</p>
                      <p className="text-xs text-muted-foreground">→ {cheque.receiver}</p>
                    </div>
                  </td>
                  <td className="px-6 py-4 text-sm font-semibold text-foreground flex items-center gap-2">
                    <DollarSign className="w-4 h-4 text-primary" />
                    {cheque.amount.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                  </td>
                  <td className="px-6 py-4 text-sm text-muted-foreground">
                    {cheque.bank}
                  </td>
                  <td className="px-6 py-4 text-sm text-muted-foreground">
                    {new Date(cheque.date).toLocaleDateString()}
                  </td>
                  <td className="px-6 py-4 text-sm">
                    <span className={`inline-block px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(cheque.status)}`}>
                      {cheque.status}
                    </span>
                  </td>
                  <td className="px-6 py-4 text-right">
                    <div className="relative">
                      <button
                        onClick={(e) => {
                          e.stopPropagation()
                          setShowActions(showActions === cheque.id ? null : cheque.id)
                        }}
                        className="p-2 hover:bg-muted rounded-lg transition-colors"
                      >
                        <MoreVertical className="w-4 h-4 text-muted-foreground" />
                      </button>

                      {showActions === cheque.id && (
                        <div className="absolute right-0 mt-2 w-48 bg-card border border-border rounded-lg shadow-lg overflow-hidden z-50">
                          <button className="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-foreground hover:bg-muted transition-colors border-b border-border">
                            <Eye className="w-4 h-4" />
                            View Details
                          </button>
                          <button className="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-foreground hover:bg-muted transition-colors border-b border-border">
                            <Download className="w-4 h-4" />
                            Download/Print
                          </button>
                          {cheque.status === 'Pending' && (
                            <button
                              onClick={() => cancelCheque(cheque.id)}
                              className="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                            >
                              <X className="w-4 h-4" />
                              Cancel Cheque
                            </button>
                          )}
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
      {cheques.length === 0 && (
        <div className="text-center py-12 bg-card rounded-2xl border border-border border-dashed">
          <h3 className="font-semibold text-foreground mb-2">No cheques yet</h3>
          <p className="text-sm text-muted-foreground mb-4">Create or upload your first cheque</p>
          <button className="px-4 py-2 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:opacity-90 transition-all duration-300 ease-in-out">
            Create Cheque
          </button>
        </div>
      )}
    </div>
  )
}
