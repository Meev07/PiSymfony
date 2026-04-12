import { CheckCircle2, AlertCircle, Info, ShieldAlert, ArrowDownLeft, X } from 'lucide-react'

const notifications = [
  {
    id: 1,
    type: 'success',
    title: 'Chèque approuvé',
    description: 'Votre chèque #CHQ001234 a été validé avec succès',
    time: 'il y a 5 min',
    unread: true,
  },
  {
    id: 2,
    type: 'info',
    title: 'Transfert reçu',
    description: 'Vous avez reçu 1,500.00 TND de Acme Corp',
    time: 'il y a 1h',
    unread: true,
  },
  {
    id: 3,
    type: 'success',
    title: 'Réclamation résolue',
    description: 'Votre réclamation #REC003 a été traitée',
    time: 'il y a 3h',
    unread: true,
  },
  {
    id: 4,
    type: 'warning',
    title: 'Alerte de connexion suspecte',
    description: 'Connexion détectée depuis un nouvel appareil à Paris, France',
    time: 'Hier',
    unread: false,
  },
  {
    id: 5,
    type: 'info',
    title: 'Mise à jour de sécurité',
    description: 'Votre mot de passe a été modifié avec succès',
    time: 'il y a 2j',
    unread: false,
  },
]

export function NotificationDropdown() {
  return (
    <div className="absolute right-0 mt-2 w-96 bg-card border border-border rounded-2xl shadow-2xl shadow-black/10 p-0 animate-in fade-in slide-in-from-top-2 duration-200 overflow-hidden">
      {/* Header */}
      <div className="flex items-center justify-between px-5 py-4 border-b border-border bg-muted/30">
        <div>
          <h3 className="font-bold text-foreground">Notifications</h3>
          <p className="text-xs text-muted-foreground mt-0.5">3 non lues</p>
        </div>
        <button className="text-xs text-primary hover:text-primary/80 font-medium transition-colors">
          Tout marquer comme lu
        </button>
      </div>

      {/* Notifications List */}
      <div className="max-h-[400px] overflow-y-auto divide-y divide-border">
        {notifications.map((notification) => {
          const Icon =
            notification.type === 'success' ? CheckCircle2 :
            notification.type === 'warning' ? ShieldAlert :
            Info

          const iconBg =
            notification.type === 'success' ? 'bg-emerald-100 dark:bg-emerald-900/30' :
            notification.type === 'warning' ? 'bg-amber-100 dark:bg-amber-900/30' :
            'bg-blue-100 dark:bg-blue-900/30'

          const iconColor =
            notification.type === 'success' ? 'text-emerald-600' :
            notification.type === 'warning' ? 'text-amber-600' :
            'text-blue-600'

          return (
            <div key={notification.id} className={`relative px-5 py-4 hover:bg-muted/40 transition-colors cursor-pointer ${notification.unread ? 'bg-primary/[0.03]' : ''}`}>
              {notification.unread && (
                <div className="absolute left-2 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-primary rounded-full" />
              )}
              <div className="flex gap-3">
                <div className={`w-9 h-9 rounded-xl ${iconBg} flex items-center justify-center flex-shrink-0`}>
                  <Icon className={`w-4 h-4 ${iconColor}`} />
                </div>
                <div className="flex-1 min-w-0">
                  <p className={`text-sm ${notification.unread ? 'font-semibold' : 'font-medium'} text-foreground`}>
                    {notification.title}
                  </p>
                  <p className="text-xs text-muted-foreground mt-0.5 line-clamp-2">
                    {notification.description}
                  </p>
                  <p className="text-[10px] text-muted-foreground mt-1.5 font-medium">
                    {notification.time}
                  </p>
                </div>
              </div>
            </div>
          )
        })}
      </div>

      {/* Footer */}
      <div className="px-5 py-3 border-t border-border bg-muted/30 text-center">
        <button className="text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
          Voir toutes les notifications
        </button>
      </div>
    </div>
  )
}
