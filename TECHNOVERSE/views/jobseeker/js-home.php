
<style>
.container {
    width: 90%;
    max-width: 1300px;
    margin: 0 auto;
}

body {
    font-family: 'Helvetica', sans-serif;
    margin: 0;
    padding: 0;
}

header {
    background-color: #fffff;
    padding: 20px;
}

header .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}


.logo {
    font-size: 28.3px;
    font-weight: bold;
}

nav ul {
    list-style: none;
    display: flex;
}

nav ul li {
    margin: 0 15px;
}

nav ul li a {
    text-decoration: none;
    color: #5A5A5A;
    font-size: 15px;
}

.hero {
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #ffbf18;
    height: 60vh;
    width: 90%;
    max-width: 1300px;
    margin: 0 auto;
    border-radius: 50px;
}

.hero-content {
    max-width: 800px;
    text-align: left;
    color: #fff;
    line-height: 1.0;
    margin-right: -20%;
}
.discover{
    font-size: 50px;
}

.categories {
    padding: 20px;
}

.category-boxes {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.category-box {
    background-color: #f2f2f2;
    width: 30%;
    height: 150px;
    border-radius: 8px;
}
h2{
    text-align: center;
    font-size: 20.3px;
    padding: 25px;
}
.hero-content {
    max-width: 50%;
    text-align: left;
    color: #fff;
    line-height: 1.0;
}

.hero-image {
    max-width: 50%;
    display: flex;
    justify-content: center;
    align-items: flex-start; /* align to top */
    padding-top: 30px; /* Adjust to move down */
}

.hero-image img {
    width: 100%;
    max-width: 750px;
    height: auto;
}
.login{
    background: #0f1035;
    height: 20px;
    border: 2px solid #0f1035;
    border-radius: 25px;
}
</style>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Technoverse</title>
</head>
<body>
<header>
    <div class="container">
        <div class="logo"><span style="color: #4f48ec;">Techno</span>verse</div>
        <nav>
            <ul>
                <li><a href="js-dashboard.php">Home</a></li>
                <li><a href="#categories">Categories</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#contact">Contact Us</a></li>
                <div class="login">
                <li><a href="../auth/logout.php" style="color: #ffffff;">Login</a></li>
                </div>
            </ul>
        </nav>
    </div>
</header>

<main>
    <section class="hero">
    <div class="container" style="display: flex; align-items: center;">
    <div class="hero-image">
        <img src="photos/in1.png" alt="Hero Image">
    </div>
    <div class="hero-content">
        <h1 class="discover">Discover Jobs<br>That Fit You</h1>
        <p style="color: black">Access curated job listings that align with your<br>qualifications, experience, and career objectives.</p>
    </div>
</div>
    </section>

    <section class="categories">
        <div class="container">
            <h2>Categories</h2>
            <div class="category-boxes">
                <div class="category-box"></div>
                <div class="category-box"></div>
                <div class="category-box"></div>
            </div>
        </div>
    </section>
</main>
</body>
</html>
