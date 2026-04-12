import Link from 'next/link'
import { Wallet } from 'lucide-react'

export const metadata = {
  title: 'Authentification — ESPRIT WALLET',
  description: 'Connexion et inscription sécurisées pour ESPRIT WALLET',
}

export default function AuthLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <div className="min-h-screen bg-gradient-to-br from-background via-background to-secondary/10 flex flex-col relative overflow-hidden">
      {/* Background Decorations */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        <div className="absolute -top-40 -right-40 w-96 h-96 bg-primary/5 rounded-full blur-3xl" />
        <div className="absolute -bottom-40 -left-40 w-96 h-96 bg-secondary/5 rounded-full blur-3xl" />
        <div className="absolute top-1/3 left-1/4 w-64 h-64 bg-primary/3 rounded-full blur-3xl" />
      </div>

      {/* Header */}
      <header className="relative border-b border-border/50 backdrop-blur-sm sticky top-0 z-50">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex items-center justify-between">
            <Link href="/auth/login" className="flex items-center gap-3 group">
              <div className="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg shadow-primary/20">
                <Wallet className="w-5 h-5 text-white" />
              </div>
              <div>
                <span className="font-bold text-lg text-foreground tracking-tight">ESPRIT WALLET</span>
                <span className="text-[10px] text-muted-foreground block -mt-0.5">Digital Finance Platform</span>
              </div>
            </Link>
            <nav className="hidden md:flex gap-8">
              <a href="#" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                Produits
              </a>
              <a href="#" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                Fonctionnalités
              </a>
              <a href="#" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                Sécurité
              </a>
            </nav>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="relative flex-1 flex items-center justify-center px-4 py-12">
        <div className="w-full max-w-md">
          {children}
        </div>
      </main>

      {/* Footer */}
      <footer className="relative border-t border-border/50 backdrop-blur-sm py-6">
        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
          <p className="text-sm text-muted-foreground text-center">
            © 2026 ESPRIT WALLET. Tous droits réservés.
          </p>
        </div>
      </footer>
    </div>
  )
}
