

		{assign var=ticketscnt value=$pageInfo.tickets|@count}
		{jstab title="Ticket History ($ticketscnt)"}
		{legend legend="Ticket History"}
		<div class="row">
			{formlabel label="Tickets" for="ticket"}
			{forminput}
			<table>
				<caption>{tr}List of CMS tickets{/tr}</caption>
				<thead>
					<tr>
						<th>Data</th>
						<th>TAG</th>
						<th>Note</th>
					</tr>
				</thead>
				<tbody>
					{section name=ticket loop=$pageInfo.tickets}
						<tr class="{cycle values="even,odd"}" title="{$pageInfo.ticket[ticket].title|escape}">
							<td>
								{$pageInfo.tickets[ticket].ticket_ref|bit_long_date} - {$pageInfo.tickets[ticket].ticket_no}
							</td>
							<td>
								{$pageInfo.tickets[ticket].tags|escape}
							</td>
							<td>
								<span class="actionicon">
									{smartlink ititle="View" ifile="view_ticket.php" ibiticon="icons/accessories-text-editor" ticket_id=$pageInfo.tickets[ticket].ticket_id}
								</span>
								<label for="ev_{$pageInfo.tickets[ticket].ticket_no}">	
									{$pageInfo.tickets[ticket].staff_id}
								</label>
							</td>
						</tr>
					{sectionelse}
						<tr class="norecords">
							<td colspan="3">
								{tr}No records found{/tr}
							</td>
						</tr>
					{/section}
				</tbody>
			</table>
			{/forminput}
		</div>
			{/legend}
			{/jstab}
