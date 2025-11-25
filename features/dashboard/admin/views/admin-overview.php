<div class="small-card" style="max-width:980px; margin:0 auto; padding:1.2rem 1.4rem;">
    <div class="dashboard-header">
        <h2 style="margin:0">Welcome</h2>
        <div>Hi, <strong><?php echo e($username); ?></strong> <span style="color: var(--muted);">(Admin)</span></div>
    </div>

    <section class="dashboard-cards">
        <a class="dashboard-card" href="<?php echo url('residents'); ?>">
            <i class="fa-solid fa-users icon" aria-hidden="true"></i>
            <h3>Residents</h3>
            <p>Manage residents and households.</p>
        </a>

        <a class="dashboard-card" href="<?php echo url('donations'); ?>">
            <i class="fa-solid fa-coins icon" aria-hidden="true"></i>
            <h3>Donations</h3>
            <p>Track donations and receipts.</p>
        </a>

        <a class="dashboard-card" href="<?php echo url('events'); ?>">
            <i class="fa-solid fa-calendar-days icon" aria-hidden="true"></i>
            <h3>Events</h3>
            <p>Plan and manage events.</p>
        </a>
    </section>
</div>
