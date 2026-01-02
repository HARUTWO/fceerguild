<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FCEER - Mock Exam Portal</title>
    
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite('resources/css/welcome.css')
</head>
<body>
    <!-- Navbar -->
    <x-navbar />

    <!-- Hero Carousel -->
    <section class="hero">
        <div id="carouselMain" class="carousel slide" data-ride="carousel" data-interval="6000">
            <ol class="carousel-indicators">
                <li data-target="#carouselMain" data-slide-to="0" class="active"></li>
                <li data-target="#carouselMain" data-slide-to="1"></li>
                <li data-target="#carouselMain" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('images/welcome/students.jpg') }}" class="d-block w-100" alt="Mock Exam Portal">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>FCEER 2025 Mock Exam Portal</h5>
                        <p>Manage your exams, schedules, and results easily in one secure platform for students and administrators.</p>
                        <p class="subsubtext">Ready ka na, future Isko?</p>
                        <div>
                            <a href="/register" class="btn-custom btn-outline-custom">Wait, Pano Yan?</a>
                            @livewire('login-modal')
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/welcome/attendance.jpg') }}" class="d-block w-100" alt="Attendance Tracker">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>FCEER Attendance Tracker Portal</h5>
                        <p>Track student attendance efficiently and keep your records up to date.</p>
                        <p class="subsubtext">Access your records anytime, anywhere.</p>
                        <div>
                            <a href="/login" class="btn-custom btn-primary-custom">Access Tracker</a>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/welcome/fceer-overview.jpg') }}" class="d-block w-100" alt="FCEER Profile">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>FCEER Profile</h5>
                        <p>Access your account details, personal records, and FCEER Profile</p>
                        <p class="subsubtext">All in one place.</p>
                        <div>
                            <a href="{{ route('profile.show') }}" class="btn-custom btn-primary-custom">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselMain" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselMain" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </section>

    <!-- Info Section -->
    <section class="info-section">
        <div class="container">
            <h2>About the FCEER Mock Exam</h2>
        </div>
    </section>

    <div class="container">
        <div class="info-slideshow-container">
            <div class="info1">
                <p>
                    Started in 2006, FCEER and its yearly review sessions and mock exams have been dedicated to helping students prepare for their College Entrance Tests (CETs). Our mission is to provide high-quality resources and practice materials that simulate the actual exam experience.
                </p>
            </div>
            
            <div class="slideshow">
                <img src="{{ asset('images/welcome/fceer-overview.jpg') }}" class="slide active" alt="FCEER Overview">
                <img src="{{ asset('images/welcome/classroom.jpg') }}" class="slide" alt="Classroom">
                <img src="{{ asset('images/welcome/students.jpg') }}" class="slide" alt="Students">
                <img src="{{ asset('images/welcome/group-session.jpg') }}" class="slide" alt="Group Session">
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} FCEER. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <!-- Scripts -->
    @vite('resources/js/welcome.js')
</body>
</html>