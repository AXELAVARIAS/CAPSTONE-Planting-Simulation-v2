<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Swipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/homepage.css">
    <style>
        body{
            background:radial-gradient(circle,rgba(40, 167, 69, .9),rgba(40, 167, 69, .5));
        }
        .card {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }
        .swipe-container {
            position: relative;
            width: 100%;
            height: 400px;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card-img-top {
            height: 300px;
            object-fit: cover;
            width: 100%;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        .buttons {
            display: flex;
            justify-content: space-around;
            margin-top: 50px;
            gap: 1.5rem;
        }
        @media (max-width: 600px) {
            .card {
                max-width: 98vw;
                margin: 0 1vw;
            }
            .swipe-container {
                height: auto;
                min-height: 320px;
            }
            .card-img-top {
                height: 180px;
            }
            .buttons {
                margin-top: 24px;
                gap: 1rem;
            }
            h1, .text-center.my-5 {
                font-size: 2rem !important;
                margin-top: 1.5rem !important;
                margin-bottom: 1.5rem !important;
            }
            .card-title {
                font-size: 1.1rem;
            }
            .card-text {
                font-size: 0.98rem;
            }
            .buttons button {
                width: 48px;
                height: 48px;
                font-size: 1.3rem;
                padding: 0;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
    <div class="container">
        <h1 class="text-center my-5 ">Swipe Plants</h1>
        <div id="swipe-container" class="swipe-container">
        </div>
        <div class="buttons">
            <button id="dislike-btn" class="btn btn-danger">
                <i class="fas fa-times"></i>
            </button>
            <button id="like-btn" class="btn btn-success">
                <i class="fas fa-heart"></i>
            </button>
        </div>
    </div>
    <script>
        const swipeContainer = document.getElementById('swipe-container');
        const likeBtn = document.getElementById('like-btn');
        const dislikeBtn = document.getElementById('dislike-btn');
        let plantCards = [];
        let currentIndex = 0;

        function fetchPlants() {
            fetch('fetchplants.php')
                .then(response => response.text())
                .then(data => {
                    swipeContainer.innerHTML = data;
                    plantCards = Array.from(document.querySelectorAll('.card'));
                    showNextPlant();
                })
                .catch(error => {
                    console.error('Error fetching plants:', error);
                    swipeContainer.innerHTML = '<p>Error loading plants.</p>';
                });
        }

        function showNextPlant() {
            plantCards.forEach((card, index) => {
                card.style.display = index === currentIndex ? 'block' : 'none';
            });
        }

        likeBtn.addEventListener('click', () => {
            if (plantCards.length > 0) {
                addFavorite(plantCards[currentIndex]);
                currentIndex = (currentIndex + 1) % plantCards.length; 
                showNextPlant();
            }
        });

        dislikeBtn.addEventListener('click', () => {
            if (plantCards.length > 0) {
                currentIndex = (currentIndex + 1) % plantCards.length; 
                showNextPlant();
            }
        });

        function addFavorite(card) {
            const plantId = card.querySelector('input[type="hidden"]').value;
            const plantName = card.querySelector('.card-title').textContent;
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "add_favorite.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log(xhr.responseText);
                }
            };
            xhr.send(`plant_id=${encodeURIComponent(plantId)}&plant_name=${encodeURIComponent(plantName)}`);
        }

        fetchPlants();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>