'use client'

import Link from 'next/link'
import {
  ArrowUpRight,
  ArrowDownLeft,
  Send,
  QrCode,
  Eye,
  EyeOff,
  TrendingUp,
  Banknote,
  PieChart,
  AlertCircle,
  Wallet,
  Plus,
  ChevronRight,
} from 'lucide-react'
import { useState } from 'react'

const balanceCards = [
  {
    id: 1,
    name: 'Compte Principal',
    type: 'Courant',
    balance: 15_234.50,
    currency: 'TND',
    accountNumber: '****2341',
    iban: 'TN59 1000 6035 ****2341',
    color: 'from-blue-600 via-blue-500 to-cyan-400',
  },
  {
    id: 2,
    name: 'Épargne',
    type: 'Épargne',
    balance: 45_678.75,
    currency: 'TND',
    accountNumber: '****7653',
    iban: 'TN59 1000 6035 ****7653',
    color: 'from-violet-600 via-purple-500 to-fuchsia-400',
  },
]

const recentTransactions = [
  { id: 1, type: 'sent', description: 'Transfert à Ahmed Ben Ali', amount: 250.00, date: "Aujourd'hui", status: 'terminé' },
  { id: 2, type: 'received', description: 'Salaire — Acme Corp', amount: 3_500.00, date: 'Hier', status: 'terminé' },
  { id: 3, type: 'sent', description: 'Facture — STEG Électricité', amount: 120.00, date: 'Il y a 3 jours', status: 'terminé' },
  { id: 4, type: 'received', description: 'Remboursement — Achat en ligne', amount: 89.99, date: 'Il y a 1 semaine', status: 'terminé' },
]

const quickActions = [
  { label: 'Envoyer', desc: 'Transfert de fonds', href: '/transactions', icon: Send, color: 'bg-blue-500/10', iconColor: 'text-blue-600' },
  { label: 'Chèques', desc: 'Gérer les chèques', href: '/cheques', icon: Banknote, color: 'bg-cyan-500/10', iconColor: 'text-cyan-600' },
  { label: 'Scanner', desc: 'Scanner un chèque', href: '/scanner', icon: QrCode, color: 'bg-violet-500/10', iconColor: 'text-violet-600' },
  { label: 'Crédit', desc: 'Simuler un crédit', href: '/credit-simulation', icon: TrendingUp, color: 'bg-emerald-500/10', iconColor: 'text-emerald-600' },
  { label: 'Réclamation', desc: 'Soumettre un ticket', href: '/reclamations', icon: AlertCircle, color: 'bg-amber-500/10', iconColor: 'text-amber-600' },
  { label: 'Nouveau Compte', desc: 'Ajouter un compte', href: '/comptes', icon: Plus, color: 'bg-rose-500/10', iconColor: 'text-rose-600' },
]

export default function DashboardPage() {
  const [showBalance, setShowBalance] = useState(true)
  const [hoveredCard, setHoveredCard] = useState<number | null>(null)
  const totalBalance = balanceCards.reduce((sum, card) => sum + card.balance, 0)

  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      {/* Header */}
      <div className="flex items-end justify-between">
        <div>
          <p className="text-sm text-muted-foreground mb-1">Bienvenue 👋</p>
          <h1 className="text-3xl md:text-4xl font-bold text-foreground">John Doe</h1>
        </div>
        <div className="text-right hidden md:block">
          <p className="text-xs text-muted-foreground">Solde Total</p>
          <p className={`text-2xl font-bold text-foreground transition-all duration-300 ${showBalance ? '' : 'blur-sm'}`}>
            {showBalance ? `${totalBalance.toLocaleString('fr-TN', { minimumFractionDigits: 2 })} TND` : '••••••'}
          </p>
        </div>
      </div>

      {/* Balance Cards */}
      <div className="grid md:grid-cols-2 gap-5">
        {balanceCards.map((card, idx) => (
          <div
            key={card.id}
            onMouseEnter={() => setHoveredCard(card.id)}
            onMouseLeave={() => setHoveredCard(null)}
            className={`group relative h-52 rounded-2xl overflow-hidden cursor-pointer transition-all duration-500 hover:scale-[1.02] hover:shadow-2xl hover:shadow-primary/20 animate-slide-up stagger-${idx + 1}`}
          >
            <div className={`absolute inset-0 bg-gradient-to-br ${card.color} animate-gradient`} />
            <div className="absolute inset-0 overflow-hidden">
              <div className="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700" />
              <div className="absolute -bottom-10 -left-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700" />
              <div className="absolute top-4 right-4 w-16 h-16 border border-white/20 rounded-xl rotate-12 group-hover:rotate-45 transition-transform duration-700" />
            </div>
            <div className="relative h-full p-6 flex flex-col justify-between text-white">
              <div className="flex items-start justify-between">
                <div>
                  <p className="text-xs opacity-80 font-medium uppercase tracking-wider">{card.type}</p>
                  <h3 className="text-lg font-bold mt-1">{card.name}</h3>
                </div>
                <div className="w-10 h-10 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center">
                  <Wallet className="w-5 h-5" />
                </div>
              </div>
              <div>
                <p className="text-xs opacity-70 mb-1">Solde Disponible</p>
                <div className="flex items-center gap-3">
                  <h2 className={`text-3xl font-bold transition-all duration-300 ${showBalance ? 'opacity-100' : 'blur-md'}`}>
                    {showBalance ? `${card.balance.toLocaleString('fr-TN', { minimumFractionDigits: 2 })} ${card.currency}` : '••••••'}
                  </h2>
                  <button onClick={(e) => { e.stopPropagation(); setShowBalance(!showBalance) }} className="p-2 hover:bg-white/20 rounded-lg transition-colors">
                    {showBalance ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                  </button>
                </div>
              </div>
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-[10px] opacity-60 uppercase tracking-wider">IBAN</p>
                  <p className="text-xs font-mono opacity-80">{card.iban}</p>
                </div>
                <div className="flex gap-1">
                  <div className="w-6 h-6 border border-white/30 rounded-md" />
                  <div className="w-6 h-6 border border-white/30 rounded-md -ml-2" />
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Quick Actions */}
      <div>
        <h2 className="text-lg font-bold text-foreground mb-4">Actions Rapides</h2>
        <div className="grid grid-cols-3 md:grid-cols-6 gap-3">
          {quickActions.map((action, idx) => {
            const Icon = action.icon
            return (
              <Link key={idx} href={action.href} className={`group p-4 bg-card rounded-2xl border border-border hover:border-primary/30 hover:shadow-lg hover:shadow-primary/5 transition-all duration-300 text-center card-hover animate-slide-up stagger-${idx + 1}`}>
                <div className={`w-12 h-12 ${action.color} rounded-xl flex items-center justify-center mx-auto group-hover:scale-110 transition-transform mb-3`}>
                  <Icon className={`w-5 h-5 ${action.iconColor}`} />
                </div>
                <p className="font-semibold text-foreground text-xs">{action.label}</p>
                <p className="text-[10px] text-muted-foreground mt-0.5 hidden md:block">{action.desc}</p>
              </Link>
            )
          })}
        </div>
      </div>

      {/* Main Content Grid */}
      <div className="grid lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="flex items-center justify-between mb-5">
            <h2 className="text-lg font-bold text-foreground">Transactions Récentes</h2>
            <Link href="/transactions" className="flex items-center gap-1 text-sm text-primary hover:text-primary/80 font-medium transition-colors">
              Voir tout <ChevronRight className="w-4 h-4" />
            </Link>
          </div>
          <div className="space-y-2">
            {recentTransactions.map((transaction) => (
              <div key={transaction.id} className="flex items-center justify-between p-3.5 hover:bg-muted/40 rounded-xl transition-all duration-200 cursor-pointer group">
                <div className="flex items-center gap-3 flex-1">
                  <div className={`w-10 h-10 rounded-xl flex items-center justify-center transition-transform group-hover:scale-110 ${transaction.type === 'sent' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-emerald-100 dark:bg-emerald-900/30'}`}>
                    {transaction.type === 'sent' ? <ArrowUpRight className="w-5 h-5 text-red-600" /> : <ArrowDownLeft className="w-5 h-5 text-emerald-600" />}
                  </div>
                  <div className="flex-1">
                    <p className="font-medium text-foreground text-sm">{transaction.description}</p>
                    <p className="text-xs text-muted-foreground mt-0.5">{transaction.date}</p>
                  </div>
                </div>
                <div className="text-right">
                  <p className={`font-bold text-sm ${transaction.type === 'sent' ? 'text-foreground' : 'text-emerald-600'}`}>
                    {transaction.type === 'sent' ? '-' : '+'}{transaction.amount.toLocaleString('fr-TN', { minimumFractionDigits: 2 })} TND
                  </p>
                  <p className="text-[10px] text-emerald-600 mt-0.5 capitalize">{transaction.status}</p>
                </div>
              </div>
            ))}
          </div>
        </div>

        <div className="space-y-6">
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
            <div className="flex items-center gap-3 mb-5">
              <div className="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                <PieChart className="w-5 h-5 text-primary" />
              </div>
              <h3 className="font-bold text-foreground">Dépenses Mensuelles</h3>
            </div>
            <div className="space-y-4">
              {[
                { label: 'Alimentation', amount: '450 TND', pct: 45, gradient: 'from-blue-500 to-cyan-400' },
                { label: 'Transport', amount: '280 TND', pct: 28, gradient: 'from-violet-500 to-purple-400' },
                { label: 'Factures', amount: '120 TND', pct: 12, gradient: 'from-emerald-500 to-teal-400' },
                { label: 'Loisirs', amount: '150 TND', pct: 15, gradient: 'from-amber-500 to-orange-400' },
              ].map((item, idx) => (
                <div key={idx}>
                  <div className="flex items-center justify-between mb-1.5">
                    <span className="text-sm text-muted-foreground">{item.label}</span>
                    <span className="font-semibold text-sm text-foreground">{item.amount}</span>
                  </div>
                  <div className="w-full bg-border rounded-full h-2">
                    <div className={`bg-gradient-to-r ${item.gradient} h-2 rounded-full transition-all duration-1000`} style={{ width: `${item.pct}%` }} />
                  </div>
                </div>
              ))}
            </div>
          </div>

          <div className="bg-gradient-to-br from-primary/10 via-secondary/5 to-primary/5 rounded-2xl border border-primary/20 shadow-md shadow-black/5 p-6">
            <h3 className="font-bold text-foreground mb-4">Santé du Compte</h3>
            <div className="space-y-3 text-sm">
              <div className="flex items-center justify-between p-2.5 bg-card/50 rounded-xl">
                <span className="text-muted-foreground">Score de crédit</span>
                <span className="font-bold text-foreground">750</span>
              </div>
              <div className="flex items-center justify-between p-2.5 bg-card/50 rounded-xl">
                <span className="text-muted-foreground">Statut</span>
                <span className="text-emerald-600 font-bold flex items-center gap-1">
                  <div className="w-2 h-2 bg-emerald-500 rounded-full" />
                  Excellent
                </span>
              </div>
              <div className="flex items-center justify-between p-2.5 bg-card/50 rounded-xl">
                <span className="text-muted-foreground">2FA</span>
                <span className="text-emerald-600 font-bold">Activé</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
