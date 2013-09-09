Staging Page
================

Author: Jake Love

Contributors: rockymountainhigh1943

Tags: WordPress, Editor, Workflow, Staging

Requires at least: 3.1

Tested up to: 3.6

Stable tag: 1.0

Version: 1.0


## Description

Staging Pages adds the ability for users to setup "staging" versions of desired posts and pages. Once a page has been setup for "Staging", the user whom initiates the staging process has control over which users get access to view and edit. The user whom initiates the staging will always have ownership.

When the page is finally ready to be deployed, simply click the **green "Deploy" button** and the new "staging" content **will overwrite the original**.

**To setup a page for Staging:**

1. Navigate to the post or page listing.
1. Hover your mouse over the desired page/post item, click the "Not Staged" link.
1. Your staging page will now be created and you'll be redirected to it's editing screen.
1. In the right hand section you'll notice a new meta box titled "Viewers / Editors", check the names for the users you'd like to have viewing and editing permissions here.
1. Along the way you can click the blue "Update" button to preview your page as you make changes.
1. When the page is finally ready to be deployed, simply click the **green "Deploy" button** and the new "staging" content **will overwrite the original**. 

**The following content can be staged:**

* Page Title
* Editor Content

**The following post types are currently supported:**

* Posts
* Pages

## Installation
1. Upload plugin files to the `/wp-content/plugins/` directory, or install using WordPress' built-in Plugin installer
1. Activate the plugin through the 'Plugins' menu in WordPress
1. See FAQ for more information about "Staging" a page

## Frequently Asked Questions
**How do I setup a page for staging?**

1. Navigate to the post or page listing.
1. Hover your mouse over the desired page/post item, click the "Not Staged" link.
1. Your staging page will now be created and you'll be redirected to it's editing screen.
1. In the right hand section you'll notice a new meta box titled "Viewers / Editors", check the names for the users you'd like to have viewing and editing permissions here.
1. Along the way you can click the blue "Update" button to preview your page as you make changes.
1. When the page is finally ready to be deployed, simply click the **green "Deploy" button** and the new "staging" content **will overwrite the original**. 


**What types of content can be staged?**

* Page Title
* Editor Content

(More features coming soon!)


**Which post types are currently supported?**

* Posts
* Pages

(Custom post types coming soon!)


**Where do I get support?**

All support requests will be answered within the support forum.


**Where are my pages staged?**

Your pages are staged within the Staging admin area. This is essentially a custom post type that stores the data for you until you're ready to deploy your changes.


**How do I know the page is staged?**

There are two ways to know your content is staged:

1. You can navigate to the post or page listing and hover over the item in question, you'll see a link titled "Staged". Clicking this link will take you to that staged content edit screen.
1. Click the "Staged Posts" or "Staged Pages" tabs. Any staged content you have permission to view/edit will be listed here.


**Why can't I see staged content?**

If you cannot locate the staged content and you are not the originating user to create the staged item, you do not have access. You will need to request access.


**Who can deploy the staged item?**

Anyone who has access to View/Edit the staged item also has access to deploy. Be careful since this action will overwrite the original content.


**How does Staging Pages handle revisions?**

Since Staging Pages is built using all WordPress native APIs and methods - when a staged item is deployed - the content will overwrite the original, but this change is stored in the revisions system. This allows users the ability to easily revert should an issue arise.

##Screenshots

1. [The Post/Page action rows add a "Status" section to show the staging status](../master/screenshot-1.png)
2. [Editing a staged item shows the Viewers / Editors meta box](../master/screenshot-2.png)
3. [The Staging item listing screen, shows the action rows for deploying the staged item](../master/screenshot-3.png)
4. [After updating the staged item the deploy button becomes available](../master/screenshot-4.png)

## Changelog
** 1.0 **
Initial release

## Upgrade Notice
** 1.0 **
Initial release