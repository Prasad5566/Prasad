#!/usr/bin/env python3
"""
Simple HTTP server for serving Prasad Maddikar's Portfolio
"""
import http.server
import socketserver
import os

class PortfolioHandler(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        # Serve static files
        super().do_GET()
    
    def end_headers(self):
        # Add CORS headers for all responses
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        super().end_headers()

def main():
    PORT = 5000
    
    # Create server
    with socketserver.TCPServer(("0.0.0.0", PORT), PortfolioHandler) as httpd:
        print(f"Portfolio server running at http://0.0.0.0:{PORT}")
        print("Serving Prasad Maddikar's Portfolio Website")
        print("Press Ctrl+C to stop the server")
        
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            print("Server stopped")

if __name__ == "__main__":
    main()