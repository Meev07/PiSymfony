'use client'

import { ChequesTable } from '@/components/modules/ChequesTable'
import { Banknote, TrendingUp, AlertCircle } from 'lucide-react'

export default function ChequesPage() {
  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      {/* Page Header */}
      <div>
        <h1 className="text-3xl font-bold text-foreground mb-2">Digital Cheques</h1>
        <p className="text-muted-foreground">Issue, receive, and manage digital cheques securely</p>
      </div>

      {/* Summary Cards */}
      <div className="grid md:grid-cols-3 gap-6">
        {/* Total Cheques */}
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Total Cheques</h3>
            <div className="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
              <Banknote className="w-5 h-5 text-primary" />
            </div>
          </div>
          <p className="text-3xl font-bold text-foreground">12</p>
          <p className="text-sm text-muted-foreground mt-2">This year</p>
        </div>

        {/* Pending Cheques */}
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Pending</h3>
            <div className="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
              <AlertCircle className="w-5 h-5 text-yellow-600" />
            </div>
          </div>
          <p className="text-3xl font-bold text-foreground">2</p>
          <p className="text-sm text-yellow-600 mt-2">Awaiting confirmation</p>
        </div>

        {/* Total Value */}
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold text-foreground">Total Value</h3>
            <div className="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <TrendingUp className="w-5 h-5 text-green-600" />
            </div>
          </div>
          <p className="text-3xl font-bold text-foreground">$57,500</p>
          <p className="text-sm text-muted-foreground mt-2">Combined value</p>
        </div>
      </div>

      {/* Cheques Table */}
      <ChequesTable />

      {/* Info Section */}
      <div className="grid md:grid-cols-2 gap-6">
        {/* How it works */}
        <div className="bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-200 dark:border-blue-900/50 p-6">
          <h3 className="font-semibold text-foreground mb-4">📋 How Digital Cheques Work</h3>
          <ol className="space-y-2 text-sm text-muted-foreground">
            <li className="flex gap-3">
              <span className="font-bold text-primary min-w-fit">1.</span>
              <span>Create a cheque with recipient details</span>
            </li>
            <li className="flex gap-3">
              <span className="font-bold text-primary min-w-fit">2.</span>
              <span>System generates unique cheque number</span>
            </li>
            <li className="flex gap-3">
              <span className="font-bold text-primary min-w-fit">3.</span>
              <span>Share with recipient or print physically</span>
            </li>
            <li className="flex gap-3">
              <span className="font-bold text-primary min-w-fit">4.</span>
              <span>Track status until confirmation</span>
            </li>
          </ol>
        </div>

        {/* Security Features */}
        <div className="bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-200 dark:border-green-900/50 p-6">
          <h3 className="font-semibold text-foreground mb-4">🔒 Security Features</h3>
          <ul className="space-y-2 text-sm text-muted-foreground">
            <li className="flex gap-2">
              <span className="text-green-600">✓</span>
              <span>Digital signatures on all cheques</span>
            </li>
            <li className="flex gap-2">
              <span className="text-green-600">✓</span>
              <span>Encryption during transmission</span>
            </li>
            <li className="flex gap-2">
              <span className="text-green-600">✓</span>
              <span>Fraud detection system</span>
            </li>
            <li className="flex gap-2">
              <span className="text-green-600">✓</span>
              <span>Cancel cheques anytime before clearing</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  )
}
