<!-- <link href="/css/bootstrap.min.css" rel="stylesheet">

<link href="/css/home.css" rel="stylesheet"> -->
@extends('app')

@section('content')
    <main role="main">

      <!-- <section class="jumbotron text-center">
        <div class="container">
          <h3 class="jumbotron-heading">Album example</h3>
          <p class="lead text-muted">Something short and leading about the collection below—its contents, the creator, etc. Make it short and sweet, but not too short so folks don't simply skip over it entirely.</p>
          <p>
            <a href="#" class="btn btn-primary">Main call to action</a>
            <a href="#" class="btn btn-secondary">Secondary action</a>
          </p>
        </div>
      </section> -->

      <div class="album text-muted">
        <div class="container">

          <div class="row">
            <div class="card">
              <img src="/picture/pic1.jpg" alt="Card image cap">
              <p class="card-text">美丽的烟花</p>
            </div>
            <div class="card">
              <img src="/picture/pic2.jpg" alt="Card image cap">
              <p class="card-text">烟花图片</p>
            </div>
            <div class="card">
              <img src="/picture/pic3.jpg" alt="Card image cap">
              <p class="card-text">烟花世界</p>
            </div>

            <div class="card">
              <img src="/picture/pic4.jpg" alt="Card image cap">
              <p class="card-text">五彩缤纷</p>
            </div>
            <div class="card">
              <img src="/picture/pic5.jpg" alt="Card image cap">
              <p class="card-text">无语伦比</p>
            </div>
            <div class="card">
              <img src="/picture/pic6.jpg" alt="Card image cap">
              <p class="card-text">田园风光</p>
            </div>

            <div class="card">
              <img src="/picture/pic7.jpg" alt="Card image cap">
              <p class="card-text">荒漠景色</p>
            </div>
            <div class="card">
              <img src="/picture/pic8.jpg" alt="Card image cap">
              <p class="card-text">江南水乡</p>
            </div>
            <div class="card">
              <img src="/picture/pic9.jpg" alt="Card image cap">
              <p class="card-text">故乡景情</p>
            </div>
            <div class="card">
              <img src="/picture/pic10.jpg" alt="Card image cap">
              <p class="card-text">幽静的乡村</p>
            </div>
          </div>

        </div>
      </div>

    </main>

    <footer class="text-muted">
      
    </footer>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="/js/jquery.min.js"><\/script>')</script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/holder.min.js"></script>
    <script>
      Holder.addTheme('thumb', {
        bg: '#55595c',
        fg: '#eceeef',
        text: 'Thumbnail'
      });
    </script>
@endsection
