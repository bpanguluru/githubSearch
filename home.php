<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="This is the homepage for search.">
<title>GitStats</title>
<!-- Link to Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<style>
  body{
        background-color: #efd0ca;
    }

  .im {
    background-color: #0a2463; 
    color: #bababa;
    height: 200px; 
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .im img {
        max-height: 100%;
        max-width: 100%; 
        object-fit: contain; 
    }
  .navbar-brand, .navbar-nav .nav-link {
    display: inline-block;
  }
  .navbar-nav {
    width: 100%;
    text-align: center;
  }
  .navbar-nav .nav-link {
    position: absolute;
    right: 0;
  }
  .card {
    height: 400px; 
    overflow: hidden; 
    background-color: #e1eded;
  }
  .card-body {
    overflow-y: auto; 
  }
  
  h2{
    color: white;
    margin-right: 26px;
    margin-left: 26px;
  }
  .error{
    color:red;
    display:none;
  }
</style>

</head>
<body>
<div class="container mt-4">
  <div class="row mb-3">
    <div class="col-12">
      <div class="card">
        <div class="im">
            <img src="imgs/github.svg" alt="githublogo">
            <h2>Search a User's Stats</h1>
            <img src="imgs/github.svg" alt="githublogo">
        </div>
        <div class="card-body">
          <input type="text" id="username" placeholder="Enter GitHub Username">
          <button onclick="fetchUserStats()">Search</button>
          <div id="errorMessage" class="error">Must enter a username</div>
          <div id="errorAPI" class="error">No user found</div>
          <h5 id = "repoCount" class="card-title">Total repo count: </h5>
          <h5 id = "forkCount"class="card-text">Total fork count: </h5>
          <h5 id = "languagesWCounts" class="card-title">Languages with counts, sorted in descending order: </h5>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  
  function fetchUserStats() {
    // take usrename from input field and make a GET req for hte php with query params
    var usernametxt = document.getElementById('username').value;
    var backendURL = 'APIEndpointNew.php?username=' + usernametxt;
    
    if (usernametxt.trim() === '') {
        document.getElementById('errorMessage').style.display = 'block';
        return;
    }
    else {
      document.getElementById('errorMessage').style.display = 'none';
    }

    fetch(backendURL)
      .then(response => response.json())
      .then(data => {
          if(data.error){
            document.getElementById('errorAPI').innerHTML = data.message;  
            document.getElementById('errorAPI').style.display = 'block';
          }
          else {
            document.getElementById('errorAPI').style.display = 'none';
            document.getElementById('repoCount').innerHTML = "Total repo count: " + data.total_repos;
            document.getElementById('forkCount').innerHTML = "Total fork count: " + data.total_forks;
            
            let languagesHTML = "Languages with counts, in descending order: <br>";
            Object.keys(data.languages).forEach(lang => 
            {
                languagesHTML += `${lang}: ${data.languages[lang]}<br>`;
            });

            document.getElementById('languagesWCounts').innerHTML = languagesHTML;
          }
      })
      .catch(error => console.error('Error api'));
  }
</script>
</body>
</html>