<?php

use yii\helpers\Url;

$this->title = 'Login';
?>

<div class="d-flex justify-content-center align-items-center vh-70 bg-light">
  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h2 class="text-center mb-4"><?= $this->title ?></h2>
    
    <form method="post" action="<?= Url::to(['auth/login']) ?>">
      <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
      
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
    </form>

    <div class="text-center mb-3">
      <p class="mb-0">Don't have an account? 
        <a href="<?= Url::to(['auth/register']) ?>" class="text-decoration-none">
          Register here
        </a>
      </p>
    </div>

    <div class="text-center">
      <a href="<?= Url::to(['/my-news-portal']) ?>" class="text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i>Back to Home
      </a>
    </div>
  </div>
</div>