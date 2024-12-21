<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://w7.pngwing.com/pngs/546/655/png-transparent-password-computer-icons-user-the-plain-style-miscellaneous-area-padlock.png" class="logo" alt="app logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
