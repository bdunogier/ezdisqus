<?php
/**
 * File containing the Comment class
 *
 * @copyright Copyright (C) 2012 Bertrand Dunogier, Jérôme Vieilledent. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License v3
 */

namespace Disqus\Export;

/**
 * Comment value object
 * @todo Implement the remote properties for SSO.
 *       See {@link https://github.com/lolautruche/eZDisqus/issues/19} and {@link http://docs.disqus.com/developers/sso/}
 */
class Comment
{
    /**
     * Comment internal (eZ Publish) ID
     *
     * @var int
     */
    public $id;

    /**
     * Author's nickname (or full name)
     *
     * @var string
     */
    public $authorName;

    /**
     * Author email.
     * Mandatory in Disqus.
     *
     * @var string
     */
    public $authorMail;

    /**
     * Author IP adress (assumed IP v4)
     *
     * @var string
     */
    public $authorIp;

    /**
     * Comment date (assumed GMT timezone)
     *
     * @var DateTime
     */
    public $date;

    /**
     * Comment content (HTML allowed)
     *
     * @var string
     */
    public $content;

    /**
     * Has the comment been approved ?
     * Default is true
     *
     * @var bool
     */
    public $isApproved = true;

    /**
     * Parent comment ID.
     * Use this if comment has been made in response to another one.
     * Default is 0 (not a reply).
     *
     * @var int
     */
    public $parentId = 0;
}
