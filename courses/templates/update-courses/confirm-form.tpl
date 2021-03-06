{extends file="form.tpl"}

{block name="form-content"}

<table class="table table-bordered table-hover table-striped">
	<thead>
		<tr>
			{foreach $fields as $field}
				<th>{$field}</th>
			{/foreach}
			<th>Include</th>
		</tr>
	</thead>
	<tbody>
		{foreach $courses as $i => $course}
			<tr>
				{foreach $fields as $field}
					<td>
						{$course[$field]}
						<input name="courses[{$i}][{$field}]" value="{$course[$field]}" type="hidden" />
					</td>
				{/foreach}
				<td>
					<div class="checkbox">
						<label for="course-{$i}-include">
							<input type="checkbox" value="include" name="courses[{$i}][batch-include]" checked="checked" />
							Include</label>
					</div>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

{assign var="formButton" value="Update"}

{/block}
