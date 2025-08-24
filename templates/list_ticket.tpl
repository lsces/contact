

		{assign var=ticketscnt value=$gContent->mInfo.tickets|@count}
		{jstab title="Ticket History ($ticketscnt)"}
		{legend legend="Ticket History"}
		<div class="form-group">
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
					{section name=ticket loop=$gContent->mInfo.tickets}
						<tr class="{cycle values="even,odd"}" title="{$gContent->mInfo.ticket[ticket].title|escape}">
							<td>
								{$gContent->mInfo.tickets[ticket].ticket_ref|bit_long_date} - {$gContent->mInfo.tickets[ticket].ticket_no}
							</td>
							<td>
								{$gContent->mInfo.tickets[ticket].tags|escape}
							</td>
							<td>
								<span class="actionicon">
									{smartlink ititle="View" ifile="view_ticket.php" booticon="icon-note-edit" ticket_id=$gContent->mInfo.tickets[ticket].ticket_id}
								</span>
								<label for="ev_{$gContent->mInfo.tickets[ticket].ticket_no}">	
									{$gContent->mInfo.tickets[ticket].staff_id}
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
