</main> <!-- Penutup main container -->

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <!-- Kolom 1: Tentang AnimeInfo -->
            <div class="col-md-4 mb-4">
                <h5><i class="fas fa-tv text-primary"></i> AnimeInfo</h5>
                <p class="small text-muted">Info jadwal tayang anime terbaru dan daftar episode lengkap</p>
            </div>
            
            <!-- Kolom 2: Menu -->
            <div class="col-md-4 mb-4">
                <h5>Menu</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php"><i class="fas fa-home me-2"></i>Home</a></li>
                    <li><a href="airing.php"><i class="fas fa-play-circle me-2"></i>Sedang Tayang</a></li>
                    <li><a href="schedule.php"><i class="fas fa-calendar-week me-2"></i>Jadwal Episode</a></li>
                    <li><a href="upcoming.php"><i class="fas fa-star me-2"></i>Akan Datang</a></li>
                </ul>
            </div>
            
            <!-- Kolom 3: Sosial Media & Informasi -->
            <div class="col-md-4 mb-4">
                <h5>Ikuti Kami</h5>
                <p class="small text-muted mb-3">Dukung terus developer website ini melalui media sosial kami.</p>
                
                <!-- Social Media Links -->
                <div class="social-links">
                    <!-- TikTok Link -->
                    <a href="https://www.tiktok.com/@darys_gm?_r=1&_t=ZS-970qohCia40" target="_blank" class="social-link tiktok" rel="noopener noreferrer">
                        <i class="fab fa-tiktok"></i>
                        <span>TikTok</span>
                    </a>
                    
                    <!-- Instagram Link -->
                    <a href="https://www.instagram.com/darsswjy?igsh=dTA1cjR3MXZqaXdr" target="_blank" class="social-link instagram" rel="noopener noreferrer">
                        <i class="fab fa-instagram"></i>
                        <span>Instagram</span>
                    </a>
                </div>
            
            </div>
        </div>
        
        <hr class="bg-secondary">
        
        <div class="text-center">
            <p class="small mb-0">&copy; 2024 AnimeInfo - All Rights Reserved</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scroll to Top Button -->
<style>
.scroll-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #3498db;
    color: white;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 999;
    border: none;
}
.scroll-to-top:hover {
    background: #2980b9;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Footer Social Links Styles */
.social-links {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 15px;
}

.social-link {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 8px 16px;
    border-radius: 30px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
    width: fit-content;
}

/* TikTok Link Style */
.social-link.tiktok {
    background: #000000;
    color: white;
    border: 1px solid #333;
}

.social-link.tiktok:hover {
    background: #161616;
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}

.social-link.tiktok i {
    font-size: 1.1rem;
}

/* Instagram Link Style */
.social-link.instagram {
    background: linear-gradient(45deg, #f09433, #d62976, #962fbf);
    color: white;
    border: none;
}

.social-link.instagram:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(210, 41, 118, 0.4);
}

.social-link.instagram i {
    font-size: 1.1rem;
}

/* Twitter Link Style (Optional) */
.social-link.twitter {
    background: #1DA1F2;
    color: white;
}

.social-link.twitter:hover {
    background: #1a8cd8;
    transform: translateX(5px);
}

/* Discord Link Style (Optional) */
.social-link.discord {
    background: #5865F2;
    color: white;
}

.social-link.discord:hover {
    background: #4752c4;
    transform: translateX(5px);
}

/* Contact Info */
.contact-info a {
    color: #bdc3c7;
    text-decoration: none;
    transition: color 0.3s ease;
}

.contact-info a:hover {
    color: #3498db;
}

/* Footer Links */
footer a {
    color: #bdc3c7;
    text-decoration: none;
    transition: all 0.3s ease;
}

footer a:hover {
    color: #3498db;
    padding-left: 5px;
}

footer h5 {
    font-size: 1.1rem;
    margin-bottom: 1rem;
    color: white;
}

footer .list-unstyled li {
    margin-bottom: 8px;
}

footer hr {
    background: #bdc3c7;
    margin: 20px 0;
}

/* Responsive Footer */
@media (max-width: 768px) {
    .social-links {
        flex-direction: row;
        flex-wrap: wrap;
    }
    
    .social-link {
        width: auto;
        padding: 6px 14px;
        font-size: 0.8rem;
    }
    
    .social-link i {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .social-link span {
        display: inline;
    }
    
    .social-link {
        padding: 5px 12px;
        font-size: 0.75rem;
    }
}
</style>

<script>
// Create scroll to top button
const scrollBtn = document.createElement('button');
scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
scrollBtn.className = 'scroll-to-top';
scrollBtn.style.display = 'none';
document.body.appendChild(scrollBtn);

scrollBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        scrollBtn.style.display = 'flex';
    } else {
        scrollBtn.style.display = 'none';
    }
});

// Auto-hide alerts
document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 5000);
});
</script>

</body>
</html>