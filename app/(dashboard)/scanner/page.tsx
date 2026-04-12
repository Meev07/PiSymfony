'use client'

import { useState } from 'react'
import {
  Upload,
  Camera,
  CheckCircle2,
  AlertCircle,
  Download,
  X,
  Zap,
} from 'lucide-react'

export default function ScannerPage() {
  const [uploadedImage, setUploadedImage] = useState<string | null>(null)
  const [scanStatus, setScanStatus] = useState<'idle' | 'processing' | 'success' | 'error'>('idle')
  const [extractedData, setExtractedData] = useState<any>(null)
  const [cameraMode, setCameraMode] = useState(false)

  const handleFileUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (file) {
      const reader = new FileReader()
      reader.onload = (event) => {
        setUploadedImage(event.target?.result as string)
        processCheque()
      }
      reader.readAsDataURL(file)
    }
  }

  const processCheque = () => {
    setScanStatus('processing')
    
    // Simulate OCR processing
    setTimeout(() => {
      setExtractedData({
        chequeNumber: 'CHQ001892',
        bankName: 'First National Bank',
        accountNumber: '****7654',
        routingNumber: '021000021',
        amount: 2500.00,
        payee: 'John Smith',
        date: '2024-04-01',
        signature: 'Valid',
        micr: '021000021 1234567890 001892',
        confidence: 0.98,
      })
      setScanStatus('success')
    }, 2000)
  }

  const resetScanner = () => {
    setUploadedImage(null)
    setScanStatus('idle')
    setExtractedData(null)
    setCameraMode(false)
  }

  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      {/* Page Header */}
      <div>
        <h1 className="text-3xl font-bold text-foreground mb-2">Cheque Scanner</h1>
        <p className="text-muted-foreground">Scan and digitize physical cheques using OCR technology</p>
      </div>

      {/* Scanner Container */}
      {!uploadedImage ? (
        <div className="space-y-6">
          {/* Upload Section */}
          <div className="bg-gradient-to-br from-primary/5 to-secondary/5 rounded-2xl border-2 border-dashed border-primary/30 p-12 text-center">
            <div className="flex justify-center mb-6">
              <div className="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center">
                <Upload className="w-8 h-8 text-primary" />
              </div>
            </div>

            <h2 className="text-2xl font-bold text-foreground mb-2">Upload Cheque Image</h2>
            <p className="text-muted-foreground mb-8 max-w-md mx-auto">
              Upload a clear image of your cheque. Both front and back work best for accurate results.
            </p>

            <div className="space-y-4 max-w-md mx-auto">
              <label className="block">
                <input
                  type="file"
                  accept="image/*"
                  onChange={handleFileUpload}
                  className="hidden"
                />
                <button
                  onClick={(e) => (e.currentTarget.parentElement?.querySelector('input') as HTMLInputElement)?.click()}
                  className="w-full px-6 py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:opacity-90 transition-all duration-300 ease-in-out active:scale-95 flex items-center justify-center gap-2"
                >
                  <Upload className="w-5 h-5" />
                  Choose File
                </button>
              </label>

              <p className="text-sm text-muted-foreground">or</p>

              <button
                onClick={() => setCameraMode(true)}
                className="w-full px-6 py-3 border border-primary text-primary rounded-lg font-medium hover:bg-primary/5 transition-all duration-300 ease-in-out flex items-center justify-center gap-2"
              >
                <Camera className="w-5 h-5" />
                Take Photo
              </button>
            </div>

            <div className="mt-8 pt-8 border-t border-border">
              <p className="text-sm text-muted-foreground mb-4 font-medium">Supported formats:</p>
              <div className="flex justify-center gap-4">
                {['JPG', 'PNG', 'PDF'].map((format) => (
                  <div key={format} className="px-3 py-1 bg-muted rounded text-sm text-foreground font-medium">
                    {format}
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Tips */}
          <div className="bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-200 dark:border-blue-900/50 p-6">
            <h3 className="font-semibold text-foreground mb-4 flex items-center gap-2">
              <Zap className="w-5 h-5 text-blue-600" />
              Tips for Best Results
            </h3>
            <ul className="space-y-2 text-sm text-muted-foreground">
              <li className="flex gap-2">
                <span>•</span>
                <span>Ensure cheque is well-lit and in focus</span>
              </li>
              <li className="flex gap-2">
                <span>•</span>
                <span>Capture the entire cheque without cropping</span>
              </li>
              <li className="flex gap-2">
                <span>•</span>
                <span>Avoid shadows and glare on the cheque</span>
              </li>
              <li className="flex gap-2">
                <span>•</span>
                <span>Images should be at least 200x200 pixels</span>
              </li>
            </ul>
          </div>
        </div>
      ) : (
        <>
          {/* Results View */}
          <div className="grid lg:grid-cols-2 gap-6">
            {/* Uploaded Image */}
            <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6">
              <h3 className="text-lg font-semibold text-foreground mb-4">Scanned Cheque</h3>
              <div className="relative bg-muted rounded-lg overflow-hidden mb-4">
                <img src={uploadedImage} alt="Uploaded cheque" className="w-full h-auto" />
              </div>
              
              {/* Processing Status */}
              {scanStatus === 'processing' && (
                <div className="space-y-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-900/50">
                  <div className="flex items-center gap-3">
                    <div className="w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin" />
                    <p className="text-sm text-blue-700 dark:text-blue-400 font-medium">Processing cheque...</p>
                  </div>
                  <div className="text-xs text-blue-600 dark:text-blue-400">
                    Extracting MICR, OCR, and signature data
                  </div>
                </div>
              )}

              {scanStatus === 'success' && (
                <div className="space-y-3 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-900/50">
                  <div className="flex items-center gap-3">
                    <CheckCircle2 className="w-5 h-5 text-green-600" />
                    <p className="text-sm text-green-700 dark:text-green-400 font-medium">Scan successful!</p>
                  </div>
                  <div className="text-xs text-green-600 dark:text-green-400">
                    Confidence: {(extractedData?.confidence * 100).toFixed(0)}%
                  </div>
                </div>
              )}

              {scanStatus === 'error' && (
                <div className="space-y-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-900/50">
                  <div className="flex items-center gap-3">
                    <AlertCircle className="w-5 h-5 text-red-600" />
                    <p className="text-sm text-red-700 dark:text-red-400 font-medium">Error processing cheque</p>
                  </div>
                  <p className="text-xs text-red-600 dark:text-red-400">
                    Please ensure the image is clear and try again
                  </p>
                </div>
              )}
            </div>

            {/* Extracted Data */}
            {extractedData && (
              <div className="bg-card rounded-2xl border border-border shadow-md shadow-black/5 p-6 space-y-6">
                <div>
                  <h3 className="text-lg font-semibold text-foreground mb-4">Extracted Information</h3>
                </div>

                {/* Data Fields */}
                <div className="space-y-4">
                  <div>
                    <label className="block text-xs font-semibold text-muted-foreground uppercase mb-1">
                      Cheque Number
                    </label>
                    <p className="text-lg font-mono font-bold text-foreground">
                      {extractedData.chequeNumber}
                    </p>
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-muted-foreground uppercase mb-1">
                      Amount
                    </label>
                    <p className="text-2xl font-bold text-primary">
                      ${extractedData.amount.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                    </p>
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-muted-foreground uppercase mb-1">
                      Payee
                    </label>
                    <p className="text-foreground font-medium">{extractedData.payee}</p>
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs font-semibold text-muted-foreground uppercase mb-1">
                        Date
                      </label>
                      <p className="text-foreground">
                        {new Date(extractedData.date).toLocaleDateString()}
                      </p>
                    </div>
                    <div>
                      <label className="block text-xs font-semibold text-muted-foreground uppercase mb-1">
                        Bank
                      </label>
                      <p className="text-foreground">{extractedData.bankName}</p>
                    </div>
                  </div>

                  <div className="pt-4 border-t border-border">
                    <label className="block text-xs font-semibold text-muted-foreground uppercase mb-1">
                      Account
                    </label>
                    <p className="text-foreground font-mono">{extractedData.accountNumber}</p>
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-muted-foreground uppercase mb-1">
                      Signature
                    </label>
                    <span className="inline-block px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-xs font-medium">
                      ✓ {extractedData.signature}
                    </span>
                  </div>
                </div>

                {/* Action Buttons */}
                <div className="space-y-3 pt-4 border-t border-border">
                  <button className="w-full px-4 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:opacity-90 transition-all duration-300 ease-in-out">
                    Save & Verify
                  </button>
                  <button
                    onClick={resetScanner}
                    className="w-full px-4 py-2.5 border border-border text-foreground rounded-lg font-medium hover:bg-muted transition-all duration-300 ease-in-out flex items-center justify-center gap-2"
                  >
                    <X className="w-4 h-4" />
                    Scan Another
                  </button>
                </div>
              </div>
            )}
          </div>
        </>
      )}

      {/* Camera Mode Placeholder */}
      {cameraMode && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
          <div className="bg-card rounded-2xl p-8 max-w-md w-full text-center">
            <h3 className="text-xl font-bold text-foreground mb-4">Camera Access</h3>
            <p className="text-muted-foreground mb-6">
              Camera functionality would be available in the mobile app. For web, please upload a file instead.
            </p>
            <button
              onClick={() => setCameraMode(false)}
              className="px-6 py-2.5 bg-primary text-primary-foreground rounded-lg font-medium hover:opacity-90 transition-all duration-300 ease-in-out"
            >
              Got it
            </button>
          </div>
        </div>
      )}
    </div>
  )
}
