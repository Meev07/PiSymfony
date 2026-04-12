import { Sidebar } from '@/components/shared/Sidebar'
import { TopBar } from '@/components/shared/TopBar'

export const metadata = {
  title: 'Dashboard - ESPRIT WALLET',
  description: 'Manage your digital wallet and transactions',
}

export default function DashboardLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <div className="min-h-screen bg-background">
      <TopBar />
      <Sidebar />
      <main className="md:ml-64 mt-16 p-4 md:p-8">
        <div className="max-w-7xl mx-auto">
          {children}
        </div>
      </main>
    </div>
  )
}
