<div class="card page-card">
    <h2>Funeral Logistics</h2>
    <p>Plan and track funeral logistics for verified notifications.</p>

    <div class="alert alert-info">
        Coordinate location, service time, transport and volunteers here.
    </div>

    <?php if (empty($funeralLogistics)): ?>
        <div class="empty-state">
            <i class="fas fa-calendar-check" style="font-size: 2rem; opacity: 0.5; margin-bottom: 1rem;"></i>
            <p>No funeral logistics recorded yet.</p>
        </div>
    <?php else: ?>
        <div class="grid-container mt-4">
            <?php foreach ($funeralLogistics as $logistics): ?>
                <div class="card">
                    <h3 class="card-title">
                        <?php echo htmlspecialchars($logistics['deceased_name'] ?? 'Funeral Arrangement'); ?>
                    </h3>
                    
                    <div class="form-group">
                        <label class="text-muted small">Burial Date</label>
                        <div><?php echo htmlspecialchars($logistics['burial_date'] ?? 'TBD'); ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="text-muted small">Burial Location</label>
                        <div><?php echo htmlspecialchars($logistics['burial_location'] ?? 'TBD'); ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="text-muted small">Grave Number</label>
                        <div><?php echo htmlspecialchars($logistics['grave_number'] ?? '-'); ?></div>
                    </div>
                    
                    <?php if (!empty($logistics['notes'])): ?>
                        <div class="form-group border-top pt-3">
                            <label class="text-muted small">Notes</label>
                            <p class="mb-0 small"><?php echo nl2br(htmlspecialchars($logistics['notes'])); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="border-top pt-3 mt-3 text-muted small">
                        Created: <?php echo isset($logistics['created_at']) ? date('d M Y H:i', strtotime($logistics['created_at'])) : 'N/A'; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
