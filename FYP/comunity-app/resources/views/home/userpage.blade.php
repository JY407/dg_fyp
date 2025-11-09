<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COM Connect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        nav {
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .navbar-nav .nav-link {
            color: black;
            font-weight: 500;
            margin: 0 10px;
        }
        .navbar-nav .nav-link:hover {
            color: #0d6efd;
        }
        .hero-section {
            text-align: center;
            padding: 80px 20px;
            background-color: #fdfdfd;
        }
        .hero-section h1 {
            font-weight: 700;
            font-size: 2.5rem;
        }
        .hero-section button {
            margin-top: 20px;
        }
        .announcement-section {
            padding: 50px 0;
            background-color: white;
        }
        .announcement-section h5 {
            font-weight: 600;
            margin-bottom: 10px;
        }
        .announcement-section .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .announcement-section .card:hover {
            transform: translateY(-5px);
        }
        footer {
            background: #fff;
            padding: 50px 0;
            border-top: 1px solid #ddd;
        }
        footer h6 {
            font-weight: 600;
            margin-bottom: 15px;
        }
        footer ul {
            list-style: none;
            padding: 0;
        }
        footer ul li {
            margin-bottom: 8px;
        }
        footer ul li a {
            color: #333;
            text-decoration: none;
        }
        footer ul li a:hover {
            color: #0d6efd;
        }
        .social-icons a {
            color: #333;
            font-size: 20px;
            margin-right: 15px;
            transition: color 0.3s;
        }
        .social-icons a:hover {
            color: #0d6efd;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-5">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-bezier2"></i>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">HOME</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">ANNOUNCEMENT</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">COMMUNITY</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">MESSAGING</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">COMPLAINT & SUGGESTION</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item me-2"><a class="nav-link" href="#">USER</a></li>
                    <li class="nav-item me-2"><button class="btn btn-outline-dark btn-sm">Sign in</button></li>
                    <li class="nav-item"><button class="btn btn-dark btn-sm">Register</button></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <h1>COM Connect</h1>
        <button class="btn btn-outline-dark">Read More</button>
    </section>

    <!-- Announcement Section -->
    <section class="announcement-section container">
        <h5>Announcement</h5>
        <p class="text-muted">HOT NEWS</p>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4 mt-3">
            @for($i=0; $i<6; $i++)
            <div class="col">
                <div class="card p-3">
                    <div class="d-flex align-items-center mb-2">
                        <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="User">
                        <span class="fw-semibold">"Quote"</span>
                    </div>
                    <h6>Title</h6>
                    <p class="text-muted mb-0">Description</p>
                </div>
            </div>
            @endfor
        </div>
    </section>

    <!-- Footer -->
    <footer class="container-fluid">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-3">
                    <h4 class="fw-bold mb-3"><i class="bi bi-bezier2"></i></h4>
                    <div class="social-icons">
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-x"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-md-3">
                    <h6>Use cases</h6>
                    <ul>
                        <li><a href="#">UI design</a></li>
                        <li><a href="#">UX design</a></li>
                        <li><a href="#">Wireframing</a></li>
                        <li><a href="#">Diagramming</a></li>
                        <li><a href="#">Online whiteboard</a></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h6>Explore</h6>
                    <ul>
                        <li><a href="#">Design</a></li>
                        <li><a href="#">Prototyping</a></li>
                        <li><a href="#">Development features</a></li>
                        <li><a href="#">Design systems</a></li>
                        <li><a href="#">FigJam</a></li>
                    </ul>
                </div>

                <div class="col-md-3">
                    <h6>Resources</h6>
                    <ul>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Best practices</a></li>
                        <li><a href="#">Support</a></li>
                        <li><a href="#">Developers</a></li>
                        <li><a href="#">Resource library</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
