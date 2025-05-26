<div class="dashboard">
    <h1>Dashboard</h1>

    <section class="announcements">
        <h2>Announcements</h2>
        <ul>
            <?php foreach ($announcements as $note): ?>
                <li><?= htmlspecialchars($note) ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="calendar">
        <h2>Upcoming Court Events</h2>
        <table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Case ID</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?= htmlspecialchars($event['Event_ID']) ?></td>
                        <td><?= htmlspecialchars($event['case_ID']) ?></td>
                        <td><?= htmlspecialchars($event['Location']) ?></td>
                        <td><?= htmlspecialchars($event['Description']) ?></td>
                        <td><?= htmlspecialchars($event['Date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>
