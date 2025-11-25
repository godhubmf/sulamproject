<div class="small-card" style="max-width:800px; margin:0 auto; padding:1.5rem;">
    <div style="margin-bottom: 1.5rem;">
        <a href="/users" style="text-decoration: none; color: var(--muted); font-size: 0.9rem;">&larr; Back to Users</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="notice error"><?php echo e($error); ?></div>
    <?php else: ?>
        <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem;">
            <h2 style="margin-bottom: 0.5rem;">Waris for <?php echo e($targetUser['name']); ?></h2>
            <div style="color: var(--muted); font-size: 0.95rem;">
                <span>@<?php echo e($targetUser['username']); ?></span> &bull; 
                <span><?php echo e($targetUser['email']); ?></span> &bull; 
                <span><?php echo e($targetUser['phone_number'] ?? 'No Phone'); ?></span>
            </div>
        </div>

        <?php if (empty($warisList)): ?>
            <div style="text-align: center; padding: 3rem; background-color: var(--bg-color); border-radius: 8px; color: var(--muted);">
                <p>This user has not added any Next of Kin (Waris) yet.</p>
            </div>
        <?php else: ?>
            <div style="display: grid; gap: 1rem;">
                <?php foreach ($warisList as $waris): ?>
                    <div style="
                        border: 1px solid var(--border-color); 
                        border-radius: 8px; 
                        padding: 1.2rem; 
                        background-color: var(--card-bg);
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                    ">
                        <div>
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.1rem;"><?php echo e($waris['name']); ?></h3>
                            <div style="display: grid; gap: 0.3rem; color: var(--text-color);">
                                <?php if (!empty($waris['phone_number'])): ?>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="opacity: 0.7;">📞</span> 
                                        <a href="tel:<?php echo e($waris['phone_number']); ?>" style="color: inherit; text-decoration: none; border-bottom: 1px dotted var(--muted);"><?php echo e($waris['phone_number']); ?></a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($waris['email'])): ?>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span style="opacity: 0.7;">✉️</span> 
                                        <span><?php echo e($waris['email']); ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($waris['address'])): ?>
                                    <div style="display: flex; align-items: flex-start; gap: 0.5rem; margin-top: 0.3rem;">
                                        <span style="opacity: 0.7;">🏠</span> 
                                        <span style="font-size: 0.9rem; opacity: 0.9;"><?php echo e($waris['address']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Future: Add Edit/Delete actions for admin here if needed -->
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
