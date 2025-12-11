<div class="card page-card">
    <h2>My Death Notifications</h2>
    <p>View death notifications you have submitted and their verification status.</p>

    <?php if (empty($userItems)): ?>
        <div class="empty-state" style="padding: 2rem; text-align: center; color: var(--muted);">
            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
            <p>You haven't submitted any death notifications yet.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Deceased Name</th>
                        <th>Date of Death</th>
                        <th>Next of Kin</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userItems as $item): ?>
                        <?php if (is_object($item)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item->deceased_name ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item->date_of_death ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item->next_of_kin_name ?? '-'); ?></td>
                            <td>
                                <?php if (!empty($item->verified)): ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> Verified
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-warning">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="small">
                                <?php echo !empty($item->created_at) ? date('M d, Y', strtotime($item->created_at)) : '-'; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
