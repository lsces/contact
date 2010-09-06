

		{assign var=ticketscnt value=$contactInfo.tickets|@count}
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
					{section name=ticket loop=$contactInfo.tickets}
						<tr class="{cycle values="even,odd"}" title="{$contactInfo.ticket[ticket].title|escape}">
							<td>
								{$contactInfo.tickets[ticket].ticket_ref|bit_long_date} - {$contactInfo.tickets[ticket].ticket_no}
							</td>
							<td>
								{$contactInfo.tickets[ticket].tags|escape}
							</td>
							<td>
								<span class="actionicon">
									{smartlink ititle="View" ifile="view_ticket.php" ibiticon="icons/accessories-text-editor" ticket_id=$contactInfo.tickets[ticket].ticket_id}
								</span>
								<label for="ev_{$contactInfo.tickets[ticket].ticket_no}">	
									{$contactInfo.tickets[ticket].staff_id}
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
