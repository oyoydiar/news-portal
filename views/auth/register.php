<?php

use yii\helpers\Url;

$this->title = 'Register an User';
?>

<div class="d-flex justify-content-center align-items-center vh-70 bg-light">
  <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h2 class="text-center mb-4"><?= $this->title ?></h2>
    <form method="post" action="<?= Url::to(['auth/register']) ?>">
      <input type="hidden" name="<?= Yii::$app->request
        ->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Retype Password</label>
        <input type="password" name="retypePassword" class="form-control" required>
      </div>

      <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>
      
      <!-- Link Login -->
      <div class="text-center">
        <p class="mb-0">
          Already have an account? 
          <a href="<?= Url::to([
            'auth/login',
          ]) ?>" class="text-decoration-none fw-bold">
            Login here
          </a>
        </p>
      </div>
    </form>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
      <div class="alert alert-danger mt-3" role="alert">
        <?= Yii::$app->session->getFlash('error') ?>
      </div>
    <?php endif; ?>
  </div>
</div>