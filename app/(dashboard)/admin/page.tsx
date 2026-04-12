'use client'

import { useState } from 'react'
import {
  Users,
  ArrowRightLeft,
  Banknote,
  AlertCircle,
  Search,
  MoreVertical,
  Eye,
  CheckCircle2,
  X,
  Shield,
  TrendingUp,
  Activity,
  FileText,
  MapPin,
  Clock,
  Filter,
  Download,
  ShieldAlert,
  UserCheck,
  UserX,
} from 'lucide-react'

/* =============== MOCK DATA =============== */

const stats = [
  { label: 'Total Utilisateurs', value: '2,847', change: '+12%', icon: Users, color: 'from-blue-500 to-cyan-500', bg: 'bg-blue-500/10' },
  { label: 'Total Transactions', value: '18,394', change: '+8.5%', icon: ArrowRightLeft, color: 'from-emerald-500 to-teal-500', bg: 'bg-emerald-500/10' },
  { label: 'Chèques en attente', value: '23', change: '-5%', icon: Banknote, color: 'from-amber-500 to-orange-500', bg: 'bg-amber-500/10' },
  { label: 'Réclamations', value: '7', change: '+2', icon: AlertCircle, color: 'from-rose-500 to-pink-500', bg: 'bg-rose-500/10' },
]

interface AdminUser {
  id: number
  name: string
  email: string
  role: 'user' | 'admin'
  status: 'active' | 'suspended' | 'pending'
  joinDate: string
  balance: number
  lastLogin: string
}

const mockUsers: AdminUser[] = [
  { id: 1, name: 'Ahmed Ben Ali', email: 'ahmed@esprit.tn', role: 'user', status: 'active', joinDate: '2024-01-15', balance: 15234.50, lastLogin: '2024-04-04' },
  { id: 2, name: 'Fatma Trabelsi', email: 'fatma@esprit.tn', role: 'user', status: 'active', joinDate: '2024-02-20', balance: 8750.00, lastLogin: '2024-04-03' },
  { id: 3, name: 'Mohamed Khelifi', email: 'mohamed@esprit.tn', role: 'admin', status: 'active', joinDate: '2023-06-10', balance: 45000.00, lastLogin: '2024-04-04' },
  { id: 4, name: 'Sara Bouazizi', email: 'sara@esprit.tn', role: 'user', status: 'suspended', joinDate: '2024-03-05', balance: 0, lastLogin: '2024-03-20' },
  { id: 5, name: 'Youssef Hamdi', email: 'youssef@esprit.tn', role: 'user', status: 'pending', joinDate: '2024-04-01', balance: 500.00, lastLogin: '2024-04-01' },
]

interface PendingCheque {
  id: number
  number: string
  sender: string
  receiver: string
  amount: number
  date: string
  status: 'pending' | 'approved' | 'rejected'
  riskScore: number
}

const mockPendingCheques: PendingCheque[] = [
  { id: 1, number: 'CHQ002001', sender: 'Ahmed Ben Ali', receiver: 'Fatma Trabelsi', amount: 5000, date: '2024-04-04', status: 'pending', riskScore: 12 },
  { id: 2, number: 'CHQ002002', sender: 'Sara Bouazizi', receiver: 'Mohamed Khelifi', amount: 15000, date: '2024-04-03', status: 'pending', riskScore: 68 },
  { id: 3, number: 'CHQ002003', sender: 'Youssef Hamdi', receiver: 'Ahmed Ben Ali', amount: 2500, date: '2024-04-03', status: 'pending', riskScore: 5 },
]

interface AdminComplaint {
  id: number
  user: string
  title: string
  category: string
  status: 'pending' | 'in-review' | 'resolved'
  date: string
  priority: 'low' | 'medium' | 'high'
}

const mockAdminComplaints: AdminComplaint[] = [
  { id: 1, user: 'Ahmed Ben Ali', title: 'Transaction non autorisée', category: 'Fraude', status: 'pending', date: '2024-04-04', priority: 'high' },
  { id: 2, user: 'Fatma Trabelsi', title: 'Problème de connexion 2FA', category: 'Technique', status: 'in-review', date: '2024-04-03', priority: 'medium' },
  { id: 3, user: 'Youssef Hamdi', title: 'Chèque non validé', category: 'Service', status: 'pending', date: '2024-04-02', priority: 'low' },
]

interface AuditLog {
  id: number
  action: string
  user: string
  ip: string
  location: string
  time: string
  type: 'login' | 'transaction' | 'admin' | 'security'
}

const mockAuditLogs: AuditLog[] = [
  { id: 1, action: 'Connexion réussie', user: 'Ahmed Ben Ali', ip: '192.168.1.45', location: 'Tunis, TN', time: 'il y a 5 min', type: 'login' },
  { id: 2, action: 'Transfert de 2,500 TND', user: 'Fatma Trabelsi', ip: '10.0.0.12', location: 'Sousse, TN', time: 'il y a 15 min', type: 'transaction' },
  { id: 3, action: 'Changement de mot de passe', user: 'Mohamed Khelifi', ip: '172.16.0.8', location: 'Sfax, TN', time: 'il y a 1h', type: 'security' },
  { id: 4, action: 'Utilisateur suspendu: Sara B.', user: 'Admin', ip: '192.168.1.1', location: 'Tunis, TN', time: 'il y a 2h', type: 'admin' },
  { id: 5, action: 'Alerte: Connexion depuis nouveau pays', user: 'Youssef Hamdi', ip: '85.214.132.117', location: 'Paris, FR', time: 'il y a 3h', type: 'security' },
]

/* =============== COMPONENT =============== */

export default function AdminDashboardPage() {
  const [activeTab, setActiveTab] = useState<'overview' | 'users' | 'cheques' | 'complaints' | 'audit'>('overview')
  const [users, setUsers] = useState(mockUsers)
  const [cheques, setCheques] = useState(mockPendingCheques)
  const [complaints, setComplaints] = useState(mockAdminComplaints)
  const [searchQuery, setSearchQuery] = useState('')
  const [showUserActions, setShowUserActions] = useState<number | null>(null)

  const toggleUserStatus = (id: number) => {
    setUsers(users.map(u =>
      u.id === id
        ? { ...u, status: u.status === 'active' ? 'suspended' as const : 'active' as const }
        : u
    ))
    setShowUserActions(null)
  }

  const approveCheque = (id: number) => {
    setCheques(cheques.map(c =>
      c.id === id ? { ...c, status: 'approved' as const } : c
    ))
  }

  const rejectCheque = (id: number) => {
    setCheques(cheques.map(c =>
      c.id === id ? { ...c, status: 'rejected' as const } : c
    ))
  }

  const updateComplaintStatus = (id: number, status: 'pending' | 'in-review' | 'resolved') => {
    setComplaints(complaints.map(c =>
      c.id === id ? { ...c, status } : c
    ))
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active': return 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
      case 'suspended': return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
      case 'pending': return 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'
      case 'approved': return 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
      case 'rejected': return 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
      case 'in-review': return 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
      case 'resolved': return 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'
      default: return 'bg-gray-100 text-gray-600'
    }
  }

  const getRiskColor = (score: number) => {
    if (score < 30) return 'text-emerald-600'
    if (score < 60) return 'text-amber-600'
    return 'text-red-600'
  }

  const getRiskBg = (score: number) => {
    if (score < 30) return 'bg-emerald-500'
    if (score < 60) return 'bg-amber-500'
    return 'bg-red-500'
  }

  const tabs = [
    { id: 'overview', label: 'Vue d\'ensemble', icon: Activity },
    { id: 'users', label: 'Utilisateurs', icon: Users },
    { id: 'cheques', label: 'Validation Chèques', icon: Banknote },
    { id: 'complaints', label: 'Réclamations', icon: AlertCircle },
    { id: 'audit', label: 'Journal d\'audit', icon: FileText },
  ]

  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      {/* Page Header */}
      <div className="flex items-start justify-between">
        <div>
          <div className="flex items-center gap-3 mb-2">
            <div className="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center">
              <Shield className="w-5 h-5 text-white" />
            </div>
            <h1 className="text-3xl font-bold text-foreground">Admin Dashboard</h1>
          </div>
          <p className="text-muted-foreground">Panneau d&apos;administration — Gérez les utilisateurs, chèques et réclamations</p>
        </div>
        <button className="flex items-center gap-2 px-4 py-2.5 bg-card border border-border rounded-xl text-sm font-medium hover:bg-muted transition-colors">
          <Download className="w-4 h-4" />
          Exporter
        </button>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map((stat, idx) => {
          const Icon = stat.icon
          return (
            <div key={idx} className={`relative overflow-hidden bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-5 card-hover animate-slide-up stagger-${idx + 1}`}>
              <div className="flex items-start justify-between mb-3">
                <div className={`w-11 h-11 ${stat.bg} rounded-xl flex items-center justify-center`}>
                  <Icon className="w-5 h-5 text-foreground" />
                </div>
                <span className={`text-xs font-bold px-2 py-1 rounded-lg ${
                  stat.change.startsWith('+') ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                }`}>
                  {stat.change}
                </span>
              </div>
              <p className="text-2xl font-bold text-foreground">{stat.value}</p>
              <p className="text-xs text-muted-foreground mt-1">{stat.label}</p>
              <div className={`absolute -right-4 -bottom-4 w-20 h-20 rounded-full bg-gradient-to-br ${stat.color} opacity-10 blur-xl`} />
            </div>
          )
        })}
      </div>

      {/* Tabs */}
      <div className="flex gap-1 bg-muted/50 rounded-xl p-1 overflow-x-auto">
        {tabs.map((tab) => {
          const Icon = tab.icon
          return (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id as any)}
              className={`flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 whitespace-nowrap ${
                activeTab === tab.id
                  ? 'bg-card text-foreground shadow-sm'
                  : 'text-muted-foreground hover:text-foreground'
              }`}
            >
              <Icon className="w-4 h-4" />
              {tab.label}
            </button>
          )
        })}
      </div>

      {/* ─── OVERVIEW TAB ─── */}
      {activeTab === 'overview' && (
        <div className="grid lg:grid-cols-2 gap-6">
          {/* Recent Activity */}
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
            <h3 className="text-lg font-bold text-foreground mb-4 flex items-center gap-2">
              <Activity className="w-5 h-5 text-primary" />
              Activité Récente
            </h3>
            <div className="space-y-3">
              {mockAuditLogs.slice(0, 4).map((log) => (
                <div key={log.id} className="flex items-center gap-3 p-3 bg-muted/30 rounded-xl">
                  <div className={`w-2 h-2 rounded-full flex-shrink-0 ${
                    log.type === 'login' ? 'bg-blue-500' :
                    log.type === 'transaction' ? 'bg-emerald-500' :
                    log.type === 'security' ? 'bg-amber-500' : 'bg-purple-500'
                  }`} />
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-foreground truncate">{log.action}</p>
                    <p className="text-xs text-muted-foreground">{log.user} • {log.time}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Risk Overview */}
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
            <h3 className="text-lg font-bold text-foreground mb-4 flex items-center gap-2">
              <ShieldAlert className="w-5 h-5 text-amber-500" />
              Détection de Risque
            </h3>
            <div className="space-y-4">
              {mockPendingCheques.map((cheque) => (
                <div key={cheque.id} className="p-4 bg-muted/30 rounded-xl">
                  <div className="flex items-center justify-between mb-2">
                    <span className="text-sm font-semibold text-foreground font-mono">{cheque.number}</span>
                    <span className={`text-sm font-bold ${getRiskColor(cheque.riskScore)}`}>
                      Risque: {cheque.riskScore}%
                    </span>
                  </div>
                  <div className="w-full bg-border rounded-full h-2 mb-2">
                    <div className={`h-2 rounded-full transition-all ${getRiskBg(cheque.riskScore)}`} style={{ width: `${cheque.riskScore}%` }} />
                  </div>
                  <p className="text-xs text-muted-foreground">
                    {cheque.sender} → {cheque.receiver} • {cheque.amount.toLocaleString()} TND
                  </p>
                </div>
              ))}
            </div>
          </div>

          {/* Location-based Login Alerts */}
          <div className="lg:col-span-2 bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 rounded-2xl border border-amber-200 dark:border-amber-800/30 p-6">
            <h3 className="text-lg font-bold text-foreground mb-4 flex items-center gap-2">
              <MapPin className="w-5 h-5 text-amber-600" />
              Alertes de Connexion Géographique
            </h3>
            <div className="grid md:grid-cols-2 gap-3">
              <div className="p-4 bg-white/80 dark:bg-card/50 rounded-xl border border-amber-200/50 dark:border-amber-800/20">
                <div className="flex items-center gap-2 mb-2">
                  <ShieldAlert className="w-4 h-4 text-amber-600" />
                  <span className="text-sm font-semibold text-foreground">Connexion inhabituelle détectée</span>
                </div>
                <p className="text-xs text-muted-foreground">Youssef Hamdi s&apos;est connecté depuis Paris, France (habituellement Tunis, TN)</p>
                <p className="text-xs text-muted-foreground mt-1">IP: 85.214.132.117 • il y a 3h</p>
                <div className="flex gap-2 mt-3">
                  <button className="px-3 py-1.5 text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg hover:bg-amber-200 dark:hover:bg-amber-900/50 transition-colors">
                    Vérifier
                  </button>
                  <button className="px-3 py-1.5 text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-600 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
                    Bloquer
                  </button>
                </div>
              </div>
              <div className="p-4 bg-white/80 dark:bg-card/50 rounded-xl border border-emerald-200/50 dark:border-emerald-800/20">
                <div className="flex items-center gap-2 mb-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-600" />
                  <span className="text-sm font-semibold text-foreground">Toutes les sessions vérifiées</span>
                </div>
                <p className="text-xs text-muted-foreground">2,842 utilisateurs connectés depuis des emplacements habituels</p>
                <p className="text-xs text-muted-foreground mt-1">Dernière vérification: il y a 5 min</p>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* ─── USERS TAB ─── */}
      {activeTab === 'users' && (
        <div className="space-y-4">
          {/* Search & Filter */}
          <div className="flex flex-col md:flex-row gap-3">
            <div className="flex-1 relative">
              <Search className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
              <input
                type="text"
                placeholder="Rechercher utilisateurs..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-10 pr-4 py-2.5 bg-card border border-border rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30"
              />
            </div>
            <div className="flex gap-2">
              <button className="flex items-center gap-2 px-4 py-2.5 bg-card border border-border rounded-xl text-sm hover:bg-muted transition-colors">
                <Filter className="w-4 h-4" /> Filtrer
              </button>
              <button className="flex items-center gap-2 px-4 py-2.5 bg-primary text-primary-foreground rounded-xl text-sm font-medium hover:opacity-90 transition-all">
                <Users className="w-4 h-4" /> Ajouter
              </button>
            </div>
          </div>

          {/* Users Table */}
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b border-border bg-muted/30">
                    <th className="px-6 py-4 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Utilisateur</th>
                    <th className="px-6 py-4 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Rôle</th>
                    <th className="px-6 py-4 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Solde</th>
                    <th className="px-6 py-4 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Statut</th>
                    <th className="px-6 py-4 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Dernière connexion</th>
                    <th className="px-6 py-4 text-right text-xs font-bold text-muted-foreground uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {users.filter(u => u.name.toLowerCase().includes(searchQuery.toLowerCase()) || u.email.toLowerCase().includes(searchQuery.toLowerCase())).map((user) => (
                    <tr key={user.id} className="hover:bg-muted/30 transition-colors">
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-3">
                          <div className="w-9 h-9 bg-gradient-to-br from-primary/80 to-secondary/80 rounded-xl flex items-center justify-center text-white text-xs font-bold">
                            {user.name.split(' ').map(n => n[0]).join('')}
                          </div>
                          <div>
                            <p className="text-sm font-semibold text-foreground">{user.name}</p>
                            <p className="text-xs text-muted-foreground">{user.email}</p>
                          </div>
                        </div>
                      </td>
                      <td className="px-6 py-4">
                        <span className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium ${
                          user.role === 'admin' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
                        }`}>
                          {user.role === 'admin' && <Shield className="w-3 h-3" />}
                          {user.role === 'admin' ? 'Admin' : 'Utilisateur'}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-sm font-semibold text-foreground">
                        {user.balance.toLocaleString('fr-TN', { minimumFractionDigits: 2 })} TND
                      </td>
                      <td className="px-6 py-4">
                        <span className={`inline-block px-2.5 py-1 rounded-lg text-xs font-medium ${getStatusColor(user.status)}`}>
                          {user.status === 'active' ? 'Actif' : user.status === 'suspended' ? 'Suspendu' : 'En attente'}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-sm text-muted-foreground">
                        {new Date(user.lastLogin).toLocaleDateString('fr-TN')}
                      </td>
                      <td className="px-6 py-4 text-right">
                        <div className="relative">
                          <button
                            onClick={() => setShowUserActions(showUserActions === user.id ? null : user.id)}
                            className="p-2 hover:bg-muted rounded-lg transition-colors"
                          >
                            <MoreVertical className="w-4 h-4 text-muted-foreground" />
                          </button>
                          {showUserActions === user.id && (
                            <div className="absolute right-0 mt-1 w-48 bg-card border border-border rounded-xl shadow-lg overflow-hidden z-50">
                              <button className="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-muted transition-colors border-b border-border">
                                <Eye className="w-4 h-4" /> Voir détails
                              </button>
                              <button
                                onClick={() => toggleUserStatus(user.id)}
                                className="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-muted transition-colors border-b border-border"
                              >
                                {user.status === 'active' ? (
                                  <><UserX className="w-4 h-4" /> Suspendre</>
                                ) : (
                                  <><UserCheck className="w-4 h-4" /> Réactiver</>
                                )}
                              </button>
                              <button className="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <X className="w-4 h-4" /> Supprimer
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
        </div>
      )}

      {/* ─── CHEQUES VALIDATION TAB ─── */}
      {activeTab === 'cheques' && (
        <div className="space-y-4">
          <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b border-border bg-muted/30">
                    <th className="px-6 py-4 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Chèque #</th>
                    <th className="px-6 py-4 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Émetteur → Bénéficiaire</th>
                    <th className="px-6 py-4 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Montant</th>
                    <th className="px-6 py-4 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Score Risque</th>
                    <th className="px-6 py-4 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Date</th>
                    <th className="px-6 py-4 text-left text-xs font-bold text-muted-foreground uppercase tracking-wider">Statut</th>
                    <th className="px-6 py-4 text-right text-xs font-bold text-muted-foreground uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border">
                  {cheques.map((cheque) => (
                    <tr key={cheque.id} className="hover:bg-muted/30 transition-colors">
                      <td className="px-6 py-4 text-sm font-mono font-bold text-foreground">{cheque.number}</td>
                      <td className="px-6 py-4">
                        <p className="text-sm font-medium text-foreground">{cheque.sender}</p>
                        <p className="text-xs text-muted-foreground">→ {cheque.receiver}</p>
                      </td>
                      <td className="px-6 py-4 text-sm font-bold text-foreground">{cheque.amount.toLocaleString()} TND</td>
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-2">
                          <div className="w-16 bg-border rounded-full h-1.5">
                            <div className={`h-1.5 rounded-full ${getRiskBg(cheque.riskScore)}`} style={{ width: `${cheque.riskScore}%` }} />
                          </div>
                          <span className={`text-xs font-bold ${getRiskColor(cheque.riskScore)}`}>{cheque.riskScore}%</span>
                        </div>
                      </td>
                      <td className="px-6 py-4 text-sm text-muted-foreground">
                        {new Date(cheque.date).toLocaleDateString('fr-TN')}
                      </td>
                      <td className="px-6 py-4">
                        <span className={`inline-block px-2.5 py-1 rounded-lg text-xs font-medium ${getStatusColor(cheque.status)}`}>
                          {cheque.status === 'pending' ? 'En attente' : cheque.status === 'approved' ? 'Approuvé' : 'Rejeté'}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-right">
                        {cheque.status === 'pending' && (
                          <div className="flex items-center justify-end gap-2">
                            <button
                              onClick={() => approveCheque(cheque.id)}
                              className="p-2 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-lg hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition-colors"
                              title="Approuver"
                            >
                              <CheckCircle2 className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => rejectCheque(cheque.id)}
                              className="p-2 bg-red-100 dark:bg-red-900/30 text-red-600 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors"
                              title="Rejeter"
                            >
                              <X className="w-4 h-4" />
                            </button>
                          </div>
                        )}
                        {cheque.status !== 'pending' && (
                          <span className="text-xs text-muted-foreground">—</span>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* ─── COMPLAINTS TAB ─── */}
      {activeTab === 'complaints' && (
        <div className="space-y-4">
          {complaints.map((complaint) => (
            <div key={complaint.id} className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-5 hover:border-primary/30 transition-all">
              <div className="flex items-start justify-between gap-4">
                <div className="flex-1">
                  <div className="flex items-center gap-3 mb-2 flex-wrap">
                    <h3 className="font-bold text-foreground">{complaint.title}</h3>
                    <span className={`px-2.5 py-1 rounded-lg text-xs font-medium ${getStatusColor(complaint.status)}`}>
                      {complaint.status === 'pending' ? 'En attente' : complaint.status === 'in-review' ? 'En cours de revue' : 'Résolu'}
                    </span>
                    <span className={`px-2 py-0.5 rounded text-xs font-medium ${
                      complaint.priority === 'high' ? 'text-red-600 bg-red-50 dark:bg-red-900/20' :
                      complaint.priority === 'medium' ? 'text-amber-600 bg-amber-50 dark:bg-amber-900/20' :
                      'text-blue-600 bg-blue-50 dark:bg-blue-900/20'
                    }`}>
                      {complaint.priority === 'high' ? 'Haute' : complaint.priority === 'medium' ? 'Moyenne' : 'Basse'} priorité
                    </span>
                  </div>
                  <div className="flex items-center gap-4 text-xs text-muted-foreground">
                    <span>Par: {complaint.user}</span>
                    <span>Catégorie: {complaint.category}</span>
                    <span>{new Date(complaint.date).toLocaleDateString('fr-TN')}</span>
                  </div>
                </div>
                <div className="flex items-center gap-2">
                  <select
                    value={complaint.status}
                    onChange={(e) => updateComplaintStatus(complaint.id, e.target.value as any)}
                    className="px-3 py-2 text-xs bg-muted/50 border border-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/30"
                  >
                    <option value="pending">En attente</option>
                    <option value="in-review">En revue</option>
                    <option value="resolved">Résolu</option>
                  </select>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* ─── AUDIT LOG TAB ─── */}
      {activeTab === 'audit' && (
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 overflow-hidden">
          <div className="px-6 py-4 border-b border-border bg-muted/30 flex items-center justify-between">
            <h3 className="font-bold text-foreground flex items-center gap-2">
              <FileText className="w-4 h-4 text-primary" />
              Journal d&apos;audit
            </h3>
            <button className="flex items-center gap-2 px-3 py-1.5 bg-card border border-border rounded-lg text-xs font-medium hover:bg-muted transition-colors">
              <Download className="w-3 h-3" /> Exporter CSV
            </button>
          </div>
          <div className="divide-y divide-border">
            {mockAuditLogs.map((log) => (
              <div key={log.id} className="px-6 py-4 flex items-center gap-4 hover:bg-muted/30 transition-colors">
                <div className={`w-2.5 h-2.5 rounded-full flex-shrink-0 ${
                  log.type === 'login' ? 'bg-blue-500' :
                  log.type === 'transaction' ? 'bg-emerald-500' :
                  log.type === 'security' ? 'bg-amber-500' : 'bg-purple-500'
                }`} />
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-foreground">{log.action}</p>
                  <div className="flex items-center gap-3 mt-0.5 text-xs text-muted-foreground">
                    <span className="font-medium">{log.user}</span>
                    <span className="flex items-center gap-1"><MapPin className="w-3 h-3" />{log.location}</span>
                    <span>IP: {log.ip}</span>
                  </div>
                </div>
                <div className="flex items-center gap-1 text-xs text-muted-foreground flex-shrink-0">
                  <Clock className="w-3 h-3" />
                  {log.time}
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}
