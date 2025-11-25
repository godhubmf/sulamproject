<div class="small-card" style="max-width:980px; margin:0 auto; padding:1.2rem 1.4rem;">
    <div class="dashboard-header">
        <h2 style="margin:0">Welcome</h2>
        <div>Hi, <strong><?php echo e($username); ?></strong></div>
    </div>

    <section class="dashboard-cards">
    <a class="dashboard-card" href="<?php echo url('donations'); ?>">
            <i class="fa-solid fa-coins icon" aria-hidden="true"></i>
            <h3>Donations</h3>
            <p>Record your donations.</p>
        </a>
        
    <a class="dashboard-card" href="<?php echo url('events'); ?>">
            <i class="fa-solid fa-calendar-days icon" aria-hidden="true"></i>
            <h3>Events</h3>
            <p>View upcoming events.</p>
        </a>
    </section>
</div>
