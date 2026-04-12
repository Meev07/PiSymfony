import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import './globals.css'

const inter = Inter({ subsets: ["latin"], variable: "--font-inter" });

export const metadata: Metadata = {
  title: 'ESPRIT WALLET — Digital Finance Platform',
  description: 'Manage your digital wallet, transactions, cheques, and finances with ESPRIT WALLET. Secure, modern, and powerful fintech platform.',
  keywords: ['digital wallet', 'fintech', 'banking', 'cheques', 'transactions', 'ESPRIT'],
  authors: [{ name: 'ESPRIT WALLET' }],
}

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  return (
    <html lang="en">
      <body className={`${inter.variable} font-sans antialiased`}>
        {children}
      </body>
    </html>
  )
}
