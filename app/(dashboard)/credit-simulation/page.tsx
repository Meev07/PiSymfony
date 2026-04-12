'use client'

import { useState } from 'react'
import { DollarSign, Calendar, Percent, TrendingDown, FileText, Share2 } from 'lucide-react'

export default function CreditSimulationPage() {
  const [formData, setFormData] = useState({
    amount: 10000,
    term: 24,
    interestRate: 5.5,
  })
  const [showResults, setShowResults] = useState(false)

  const monthlyPayment = (
    (formData.amount * (formData.interestRate / 100 / 12)) /
    (1 - Math.pow(1 + formData.interestRate / 100 / 12, -formData.term))
  )

  const totalPayment = monthlyPayment * formData.term
  const totalInterest = totalPayment - formData.amount

  const handleSimulate = (e: React.FormEvent) => {
    e.preventDefault()
    setShowResults(true)
  }

  const amortizationSchedule = Array.from({ length: Math.min(formData.term, 12) }, (_, i) => {
    const monthNumber = i + 1
    let balance = formData.amount
    let totalInterestPaid = 0

    for (let month = 1; month <= monthNumber; month++) {
      const interestPayment = balance * (formData.interestRate / 100 / 12)
      const principalPayment = monthlyPayment - interestPayment
      totalInterestPaid += interestPayment
      balance -= principalPayment
    }

    return {
      month: monthNumber,
      payment: monthlyPayment,
      principal: monthlyPayment - (balance * (formData.interestRate / 100 / 12)),
      interest: balance * (formData.interestRate / 100 / 12),
      balance: Math.max(0, balance),
    }
  })

  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      {/* Page Header */}
      <div>
        <h1 className="text-3xl font-bold text-foreground mb-2">Credit Simulator</h1>
        <p className="text-muted-foreground">Calculate loan payments and view amortization schedules</p>
      </div>

      <div className="grid lg:grid-cols-2 gap-8">
        {/* Calculator */}
        <div>
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-8 sticky top-24">
            <h2 className="text-xl font-bold text-foreground mb-6">Loan Calculator</h2>

            <form onSubmit={handleSimulate} className="space-y-6">
              {/* Loan Amount */}
              <div>
                <label className="block text-sm font-semibold text-foreground mb-3 flex items-center gap-2">
                  <DollarSign className="w-4 h-4 text-primary" />
                  Loan Amount
                </label>
                <div className="space-y-3">
                  <input
                    type="range"
                    min="1000"
                    max="100000"
                    step="1000"
                    value={formData.amount}
                    onChange={(e) => setFormData({ ...formData, amount: parseFloat(e.target.value) })}
                    className="w-full h-2 bg-border rounded-lg appearance-none cursor-pointer accent-primary"
                  />
                  <div className="flex items-center gap-2">
                    <span className="text-3xl font-bold text-primary">
                      ${formData.amount.toLocaleString()}
                    </span>
                    <input
                      type="number"
                      value={formData.amount}
                      onChange={(e) => setFormData({ ...formData, amount: parseFloat(e.target.value) })}
                      className="ml-auto w-32 px-3 py-2 bg-input border border-border rounded-lg text-foreground text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                    />
                  </div>
                </div>
              </div>

              {/* Loan Term */}
              <div>
                <label className="block text-sm font-semibold text-foreground mb-3 flex items-center gap-2">
                  <Calendar className="w-4 h-4 text-secondary" />
                  Loan Term
                </label>
                <div className="space-y-3">
                  <input
                    type="range"
                    min="6"
                    max="360"
                    step="1"
                    value={formData.term}
                    onChange={(e) => setFormData({ ...formData, term: parseFloat(e.target.value) })}
                    className="w-full h-2 bg-border rounded-lg appearance-none cursor-pointer accent-secondary"
                  />
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-2xl font-bold text-secondary">{formData.term}</p>
                      <p className="text-sm text-muted-foreground">months</p>
                    </div>
                    <div className="text-right">
                      <p className="text-2xl font-bold text-foreground">
                        {(formData.term / 12).toFixed(1)}
                      </p>
                      <p className="text-sm text-muted-foreground">years</p>
                    </div>
                  </div>
                </div>
              </div>

              {/* Interest Rate */}
              <div>
                <label className="block text-sm font-semibold text-foreground mb-3 flex items-center gap-2">
                  <Percent className="w-4 h-4 text-purple-500" />
                  Annual Interest Rate
                </label>
                <div className="space-y-3">
                  <input
                    type="range"
                    min="0"
                    max="20"
                    step="0.1"
                    value={formData.interestRate}
                    onChange={(e) => setFormData({ ...formData, interestRate: parseFloat(e.target.value) })}
                    className="w-full h-2 bg-border rounded-lg appearance-none cursor-pointer accent-purple-500"
                  />
                  <div className="flex items-center gap-2">
                    <span className="text-3xl font-bold text-purple-500">
                      {formData.interestRate.toFixed(2)}%
                    </span>
                    <input
                      type="number"
                      step="0.1"
                      value={formData.interestRate}
                      onChange={(e) => setFormData({ ...formData, interestRate: parseFloat(e.target.value) })}
                      className="ml-auto w-32 px-3 py-2 bg-input border border-border rounded-lg text-foreground text-sm focus:outline-none focus:ring-2 focus:ring-primary/50"
                    />
                  </div>
                </div>
              </div>

              {/* Calculate Button */}
              <button
                type="submit"
                className="w-full px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-lg font-bold hover:opacity-90 transition-all duration-300 ease-in-out mt-8"
              >
                Calculate Loan
              </button>
            </form>
          </div>
        </div>

        {/* Results */}
        <div className="space-y-6">
          {showResults && (
            <>
              {/* Summary Cards */}
              <div className="grid sm:grid-cols-2 gap-4">
                <div className="bg-gradient-to-br from-primary/10 to-primary/5 rounded-2xl border border-primary/20 p-6">
                  <p className="text-sm text-muted-foreground mb-1">Monthly Payment</p>
                  <p className="text-3xl font-bold text-primary">
                    ${monthlyPayment.toFixed(2)}
                  </p>
                </div>
                <div className="bg-gradient-to-br from-secondary/10 to-secondary/5 rounded-2xl border border-secondary/20 p-6">
                  <p className="text-sm text-muted-foreground mb-1">Total Interest</p>
                  <p className="text-3xl font-bold text-secondary">
                    ${totalInterest.toFixed(2)}
                  </p>
                </div>
                <div className="bg-gradient-to-br from-green-500/10 to-green-500/5 rounded-2xl border border-green-500/20 p-6">
                  <p className="text-sm text-muted-foreground mb-1">Loan Amount</p>
                  <p className="text-3xl font-bold text-green-600">
                    ${formData.amount.toLocaleString()}
                  </p>
                </div>
                <div className="bg-gradient-to-br from-blue-500/10 to-blue-500/5 rounded-2xl border border-blue-500/20 p-6">
                  <p className="text-sm text-muted-foreground mb-1">Total Payment</p>
                  <p className="text-3xl font-bold text-blue-600">
                    ${totalPayment.toFixed(2)}
                  </p>
                </div>
              </div>

              {/* Breakdown */}
              <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
                <h3 className="text-lg font-bold text-foreground mb-4">Payment Breakdown</h3>
                <div className="space-y-4">
                  <div>
                    <div className="flex items-center justify-between mb-2">
                      <span className="text-sm text-muted-foreground">Principal</span>
                      <span className="font-semibold text-foreground">
                        ${formData.amount.toLocaleString()}
                      </span>
                    </div>
                    <div className="w-full bg-border rounded-full h-3">
                      <div
                        className="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full"
                        style={{ width: `${(formData.amount / totalPayment) * 100}%` }}
                      />
                    </div>
                  </div>
                  <div>
                    <div className="flex items-center justify-between mb-2">
                      <span className="text-sm text-muted-foreground">Interest</span>
                      <span className="font-semibold text-foreground">
                        ${totalInterest.toFixed(2)}
                      </span>
                    </div>
                    <div className="w-full bg-border rounded-full h-3">
                      <div
                        className="bg-gradient-to-r from-orange-500 to-red-500 h-3 rounded-full"
                        style={{ width: `${(totalInterest / totalPayment) * 100}%` }}
                      />
                    </div>
                  </div>
                </div>
              </div>

              {/* Action Buttons */}
              <div className="flex gap-3">
                <button className="flex-1 flex items-center justify-center gap-2 px-4 py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:opacity-90 transition-all duration-300 ease-in-out">
                  <FileText className="w-4 h-4" />
                  Generate PDF
                </button>
                <button className="flex-1 flex items-center justify-center gap-2 px-4 py-3 border border-border text-foreground rounded-lg font-medium hover:bg-muted transition-all duration-300 ease-in-out">
                  <Share2 className="w-4 h-4" />
                  Share
                </button>
              </div>

              {/* Amortization Schedule */}
              <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
                <h3 className="text-lg font-bold text-foreground mb-4 flex items-center gap-2">
                  <TrendingDown className="w-5 h-5 text-primary" />
                  First 12 Months
                </h3>
                <div className="overflow-x-auto">
                  <table className="w-full text-sm">
                    <thead>
                      <tr className="border-b border-border">
                        <th className="text-left py-2 text-muted-foreground font-semibold">Month</th>
                        <th className="text-right py-2 text-muted-foreground font-semibold">Payment</th>
                        <th className="text-right py-2 text-muted-foreground font-semibold">Principal</th>
                        <th className="text-right py-2 text-muted-foreground font-semibold">Interest</th>
                        <th className="text-right py-2 text-muted-foreground font-semibold">Balance</th>
                      </tr>
                    </thead>
                    <tbody>
                      {amortizationSchedule.map((row) => (
                        <tr key={row.month} className="border-b border-border hover:bg-muted/50">
                          <td className="py-2 font-medium text-foreground">{row.month}</td>
                          <td className="text-right text-foreground">${row.payment.toFixed(2)}</td>
                          <td className="text-right text-green-600">${row.principal.toFixed(2)}</td>
                          <td className="text-right text-orange-600">${row.interest.toFixed(2)}</td>
                          <td className="text-right font-medium text-foreground">
                            ${row.balance.toFixed(2)}
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  )
}
