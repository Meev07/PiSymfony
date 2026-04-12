'use client'

import { useState } from 'react'
import {
  AlertCircle,
  MessageSquare,
  MoreVertical,
  Plus,
  Clock,
  CheckCircle2,
  X,
  FileText,
} from 'lucide-react'

interface Complaint {
  id: number
  title: string
  description: string
  category: 'billing' | 'technical' | 'service' | 'fraud' | 'other'
  status: 'open' | 'in-progress' | 'resolved' | 'closed'
  priority: 'low' | 'medium' | 'high'
  date: string
  lastUpdate: string
  attachments: number
}

const mockComplaints: Complaint[] = [
  {
    id: 1,
    title: 'Unauthorized transaction detected',
    description: 'There was a transaction of $200 that I did not authorize',
    category: 'fraud',
    status: 'in-progress',
    priority: 'high',
    date: '2024-04-03',
    lastUpdate: '2024-04-04',
    attachments: 1,
  },
  {
    id: 2,
    title: 'Cheque not cleared after 5 days',
    description: 'Deposited cheque is still pending despite clearing timeframe',
    category: 'service',
    status: 'open',
    priority: 'medium',
    date: '2024-04-01',
    lastUpdate: '2024-04-01',
    attachments: 2,
  },
  {
    id: 3,
    title: 'App login issue',
    description: 'Unable to login to app since yesterday',
    category: 'technical',
    status: 'resolved',
    priority: 'high',
    date: '2024-03-28',
    lastUpdate: '2024-03-29',
    attachments: 0,
  },
]

export default function ReclamationsPage() {
  const [complaints, setComplaints] = useState<Complaint[]>(mockComplaints)
  const [showNewForm, setShowNewForm] = useState(false)
  const [showActions, setShowActions] = useState<number | null>(null)
  const [selectedComplaint, setSelectedComplaint] = useState<Complaint | null>(null)
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    category: 'billing',
    priority: 'medium',
  })

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    if (formData.title && formData.description) {
      const newComplaint: Complaint = {
        id: complaints.length + 1,
        title: formData.title,
        description: formData.description,
        category: formData.category as any,
        status: 'open',
        priority: formData.priority as any,
        date: new Date().toISOString().split('T')[0],
        lastUpdate: new Date().toISOString().split('T')[0],
        attachments: 0,
      }
      setComplaints([newComplaint, ...complaints])
      setFormData({ title: '', description: '', category: 'billing', priority: 'medium' })
      setShowNewForm(false)
    }
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'open':
        return 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
      case 'in-progress':
        return 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400'
      case 'resolved':
        return 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
      case 'closed':
        return 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-400'
      default:
        return 'bg-gray-100'
    }
  }

  const getPriorityColor = (priority: string) => {
    switch (priority) {
      case 'low':
        return 'text-blue-600'
      case 'medium':
        return 'text-yellow-600'
      case 'high':
        return 'text-red-600'
      default:
        return 'text-gray-600'
    }
  }

  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      {/* Page Header */}
      <div>
        <h1 className="text-3xl font-bold text-foreground mb-2">Complaints & Support</h1>
        <p className="text-muted-foreground">Submit and track your support requests</p>
      </div>

      {/* Summary Stats */}
      <div className="grid md:grid-cols-4 gap-6">
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="text-2xl font-bold text-foreground">{complaints.length}</div>
          <p className="text-sm text-muted-foreground mt-1">Total Requests</p>
        </div>
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="text-2xl font-bold text-yellow-600">
            {complaints.filter(c => c.status === 'open').length}
          </div>
          <p className="text-sm text-muted-foreground mt-1">Open</p>
        </div>
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="text-2xl font-bold text-blue-600">
            {complaints.filter(c => c.status === 'in-progress').length}
          </div>
          <p className="text-sm text-muted-foreground mt-1">In Progress</p>
        </div>
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
          <div className="text-2xl font-bold text-green-600">
            {complaints.filter(c => c.status === 'resolved').length}
          </div>
          <p className="text-sm text-muted-foreground mt-1">Resolved</p>
        </div>
      </div>

      {/* New Complaint Form */}
      {showNewForm && (
        <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6 space-y-4">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-lg font-bold text-foreground">Submit New Complaint</h3>
            <button onClick={() => setShowNewForm(false)} className="p-2 hover:bg-muted rounded">
              <X className="w-5 h-5" />
            </button>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Title</label>
              <input
                type="text"
                placeholder="Brief description of your complaint"
                value={formData.title}
                onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-foreground mb-2">Description</label>
              <textarea
                placeholder="Provide detailed information about your issue"
                value={formData.description}
                onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                rows={4}
                className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground placeholder-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"
              />
            </div>

            <div className="grid md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-foreground mb-2">Category</label>
                <select
                  value={formData.category}
                  onChange={(e) => setFormData({ ...formData, category: e.target.value })}
                  className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
                >
                  <option value="billing">Billing Issue</option>
                  <option value="technical">Technical Issue</option>
                  <option value="service">Service Issue</option>
                  <option value="fraud">Fraud Report</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div>
                <label className="block text-sm font-medium text-foreground mb-2">Priority</label>
                <select
                  value={formData.priority}
                  onChange={(e) => setFormData({ ...formData, priority: e.target.value })}
                  className="w-full px-4 py-2.5 bg-input border border-border rounded-lg text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50"
                >
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
              </div>
            </div>

            <div className="flex gap-3 pt-2">
              <button
                type="submit"
                className="flex-1 px-4 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:opacity-90 transition-all duration-300 ease-in-out"
              >
                Submit Complaint
              </button>
              <button
                type="button"
                onClick={() => setShowNewForm(false)}
                className="flex-1 px-4 py-2.5 border border-border text-foreground rounded-lg font-medium hover:bg-muted transition-all duration-300 ease-in-out"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Create Button */}
      {!showNewForm && (
        <button
          onClick={() => setShowNewForm(true)}
          className="flex items-center gap-2 px-6 py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:opacity-90 transition-all duration-300 ease-in-out"
        >
          <Plus className="w-5 h-5" />
          Submit New Complaint
        </button>
      )}

      {/* Complaints List */}
      <div className="space-y-4">
        {complaints.length > 0 ? (
          complaints.map((complaint) => (
            <div
              key={complaint.id}
              onClick={() => setSelectedComplaint(complaint)}
              className="bg-card rounded-xl border border-border shadow-md shadow-black/5 p-6 hover:border-primary/50 hover:shadow-md transition-all cursor-pointer"
            >
              <div className="flex items-start justify-between gap-4">
                <div className="flex-1">
                  <div className="flex items-center gap-3 mb-2">
                    <h3 className="font-semibold text-foreground text-lg">{complaint.title}</h3>
                    <span className={`px-2 py-1 rounded text-xs font-medium ${getStatusColor(complaint.status)}`}>
                      {complaint.status.replace('-', ' ')}
                    </span>
                  </div>

                  <p className="text-muted-foreground text-sm mb-3">{complaint.description}</p>

                  <div className="flex items-center gap-4 flex-wrap text-xs text-muted-foreground">
                    <div className="flex items-center gap-1">
                      <AlertCircle className={`w-4 h-4 ${getPriorityColor(complaint.priority)}`} />
                      <span className="capitalize">{complaint.priority} Priority</span>
                    </div>
                    <div className="flex items-center gap-1">
                      <Clock className="w-4 h-4" />
                      <span>Submitted {new Date(complaint.date).toLocaleDateString()}</span>
                    </div>
                    <div className="flex items-center gap-1">
                      <FileText className="w-4 h-4" />
                      <span>{complaint.attachments} attachment{complaint.attachments !== 1 ? 's' : ''}</span>
                    </div>
                  </div>
                </div>

                <div className="relative flex-shrink-0">
                  <button
                    onClick={(e) => {
                      e.stopPropagation()
                      setShowActions(showActions === complaint.id ? null : complaint.id)
                    }}
                    className="p-2 hover:bg-muted rounded"
                  >
                    <MoreVertical className="w-5 h-5 text-muted-foreground" />
                  </button>

                  {showActions === complaint.id && (
                    <div className="absolute right-0 mt-2 w-40 bg-card border border-border rounded-lg shadow-lg overflow-hidden z-50">
                      <button className="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-foreground hover:bg-muted transition-colors border-b border-border">
                        <MessageSquare className="w-4 h-4" />
                        Add Reply
                      </button>
                      <button className="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <X className="w-4 h-4" />
                        Close
                      </button>
                    </div>
                  )}
                </div>
              </div>
            </div>
          ))
        ) : (
          <div className="text-center py-12 bg-card rounded-2xl border border-border border-dashed">
            <CheckCircle2 className="w-12 h-12 text-green-600 mx-auto mb-3 opacity-50" />
            <p className="text-muted-foreground mb-2">No complaints yet</p>
            <p className="text-sm text-muted-foreground">We&apos;re happy to hear everything is working smoothly!</p>
          </div>
        )}
      </div>

      {/* FAQ Section */}
      <div className="bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-200 dark:border-blue-900/50 p-6">
        <h3 className="font-semibold text-foreground mb-4">📞 Quick Support Options</h3>
        <div className="grid md:grid-cols-2 gap-4">
          <button className="p-4 bg-white dark:bg-blue-950/50 rounded-lg border border-blue-200 dark:border-blue-900 hover:border-primary transition-colors text-left">
            <p className="font-medium text-foreground mb-1">Live Chat</p>
            <p className="text-sm text-muted-foreground">Chat with our support team</p>
          </button>
          <button className="p-4 bg-white dark:bg-blue-950/50 rounded-lg border border-blue-200 dark:border-blue-900 hover:border-primary transition-colors text-left">
            <p className="font-medium text-foreground mb-1">Email Support</p>
            <p className="text-sm text-muted-foreground">support@espritwallet.com</p>
          </button>
        </div>
      </div>
    </div>
  )
}
