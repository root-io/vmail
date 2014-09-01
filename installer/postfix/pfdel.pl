#!/usr/bin/perl -w
#
# pfdel - deletes message containing specified address from
# Postfix queue. Matches either sender or recipient address.
#
# Usage: pfdel <email_address>
#

use strict;

# Change these paths if necessary.
my $LISTQ = "/usr/sbin/postqueue -p";
my $POSTSUPER = "/usr/sbin/postsuper";

my $email_addr = "";
my $qid = "";
my $euid = $>;

if ( @ARGV !=  1 ) {
	die "Usage: pfdel <email_address>\n";
} else {
	$email_addr = $ARGV[0];
}

if ( $euid != 0 ) {
        die "You must be root to delete queue files.\n";
}


open(QUEUE, "$LISTQ |") || 
  die "Can't get pipe to $LISTQ: $!\n";

my $entry = <QUEUE>;	# skip single header line
$/ = "";		# Rest of queue entries print on
			# multiple lines.
while ( $entry = <QUEUE> ) {
	if ( $entry =~ / $email_addr$/m ) {
		($qid) = split(/\s+/, $entry, 2);
		$qid =~ s/[\*\!]//;
		next unless ($qid);

		#
		# Execute postsuper -d with the queue id.
		# postsuper provides feedback when it deletes
		# messages. Let its output go through.
		#
		if ( system($POSTSUPER, "-d", $qid) != 0 ) {
			# If postsuper has a problem, bail.
			die "Error executing $POSTSUPER: error " .
			   "code " .  ($?/256) . "\n";
		}
	}
}
close(QUEUE);

if (! $qid ) {
	die "No messages with the address <$email_addr> " .
	  "found in queue.\n";
}

exit 0;