import Link from 'next/link'
import { User, Lock, CreditCard, LogOut, Settings, Shield, Activity } from 'lucide-react'

export function UserProfileDropdown() {
  return (
    <div className="absolute right-0 mt-2 w-72 bg-card border border-border rounded-2xl shadow-2xl shadow-black/10 overflow-hidden animate-in fade-in slide-in-from-top-2 duration-200">
      {/* User Info */}
      <div className="px-5 py-4 border-b border-border bg-gradient-to-br from-primary/5 to-secondary/5">
        <div className="flex items-center gap-3">
          <div className="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg shadow-primary/20">
            JD
          </div>
          <div className="flex-1 min-w-0">
            <p className="font-bold text-foreground">John Doe</p>
            <p className="text-xs text-muted-foreground truncate">john@esprit.tn</p>
            <span className="inline-block mt-1 px-2 py-0.5 text-[9px] font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-full uppercase tracking-wider">
              Vérifié
            </span>
          </div>
        </div>
      </div>

      {/* Menu Items */}
      <nav className="py-2">
        <Link href="/settings" className="flex items-center gap-3 px-5 py-2.5 text-sm text-foreground hover:bg-muted/60 transition-colors group">
          <div className="w-8 h-8 bg-muted/50 rounded-lg flex items-center justify-center group-hover:bg-primary/10 transition-colors">
            <User className="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors" />
          </div>
          <div>
            <p className="font-medium">Mon Profil</p>
            <p className="text-[10px] text-muted-foreground">Voir et modifier votre profil</p>
          </div>
        </Link>
        <Link href="/settings" className="flex items-center gap-3 px-5 py-2.5 text-sm text-foreground hover:bg-muted/60 transition-colors group">
          <div className="w-8 h-8 bg-muted/50 rounded-lg flex items-center justify-center group-hover:bg-primary/10 transition-colors">
            <Shield className="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors" />
          </div>
          <div>
            <p className="font-medium">Sécurité</p>
            <p className="text-[10px] text-muted-foreground">2FA, mot de passe</p>
          </div>
        </Link>
        <Link href="/settings" className="flex items-center gap-3 px-5 py-2.5 text-sm text-foreground hover:bg-muted/60 transition-colors group">
          <div className="w-8 h-8 bg-muted/50 rounded-lg flex items-center justify-center group-hover:bg-primary/10 transition-colors">
            <Activity className="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors" />
          </div>
          <div>
            <p className="font-medium">Activité de connexion</p>
            <p className="text-[10px] text-muted-foreground">Historique des sessions</p>
          </div>
        </Link>
        <Link href="/settings" className="flex items-center gap-3 px-5 py-2.5 text-sm text-foreground hover:bg-muted/60 transition-colors group">
          <div className="w-8 h-8 bg-muted/50 rounded-lg flex items-center justify-center group-hover:bg-primary/10 transition-colors">
            <Settings className="w-4 h-4 text-muted-foreground group-hover:text-primary transition-colors" />
          </div>
          <div>
            <p className="font-medium">Paramètres</p>
            <p className="text-[10px] text-muted-foreground">Préférences du compte</p>
          </div>
        </Link>
      </nav>

      {/* Divider */}
      <div className="border-t border-border" />

      {/* Logout */}
      <div className="p-2">
        <Link href="/auth/login" className="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors font-medium">
          <LogOut className="w-4 h-4" />
          Déconnexion
        </Link>
      </div>
    </div>
  )
}
