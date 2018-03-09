<?php

namespace Kirby\Cms;

use Exception;
use Kirby\Base\Base;
use Kirby\Util\Dir;
use Kirby\Util\F;

class PageStore extends PageStoreDefault
{

    const PAGE_STORE_CLASS = PageStore::class;
    const FILE_STORE_CLASS = FileStore::class;

    protected $base;
    protected $root;

    public function base()
    {
        if (is_a($this->base, Base::class) === true) {
            return $this->base;
        }

        return $this->base = new Base([
            'extension' => 'txt',
            'root'      => $this->root()
        ]);
    }

    public function changeNum(int $num = null)
    {
        if ($this->exists() === false) {
            return parent::changeNum($num);
        }

        $oldPage = $this->page();
        $oldRoot = $this->root();

        $newPage = parent::changeNum($num);
        $newRoot = $this->root($newPage);

        $this->moveDirectory($oldRoot, $newRoot);

        return $newPage;
    }

    public function changeSlug(string $slug)
    {
        if ($this->exists() === false) {
            return parent::changeSlug($slug);
        }

        $oldPage = $this->page();
        $oldRoot = $this->root();

        $newPage = parent::changeSlug($slug);
        $newRoot = $this->root($newPage);

        $this->moveDirectory($oldRoot, $newRoot);

        $this->media()->delete($oldPage);

        return $newPage;
    }

    public function changeTemplate(string $template)
    {
        if ($this->exists() === false) {
            return parent::changeTemplate($template);
        }

        $oldPage = $this->page();
        $oldFile = $this->base()->storage();

        $newPage = parent::changeTemplate($template);
        $newFile = $this->root() . '/' . $newPage->template() . '.' . $this->base()->extension();

        if (F::move($oldFile, $newFile) !== true) {
            throw new Exception('The text file could not be moved');
        }

        return $newPage;
    }

    public function children(): Children
    {

        $parent    = $this->page();
        $id        = $parent->id();
        $url       = $parent->url();
        $site      = $parent->site();
        $extension = $this->base()->extension();
        $children  = new Children([], $parent);

        foreach ($this->base()->children() as $slug => $props) {

            $page = Page::factory([
                'num'    => $props['num'],
                'parent' => $parent,
                'site'   => $site,
                'slug'   => $slug,
                'store'  => static::PAGE_STORE_CLASS
            ]);

            $children->set($page->id(), $page);

        }

        return $children;

    }

    public function content()
    {
        return $this->base()->read();
    }

    public function create(Page $page)
    {
        if ($this->exists() === true) {
            return $page;
        }

        $root = $this->root($page);

        // create the new page directory
        if (Dir::make($root) !== true) {
            throw new Exception('The page directory cannot be created');
        }

        // write the text file
        touch($root . '/' . $page->template() . '.txt');

        // write the content file
        return $page->update(null, false);
    }

    public function delete(): bool
    {
        // delete the content folder for this page
        return $this->base()->delete();
    }

    public function exists(): bool
    {
        return is_dir($this->base()->root()) === true;
    }

    public function files(): Files
    {
        $base      = $this->base();
        $page      = $this->page();
        $url       = $this->media()->url($page);
        $extension = $base->extension();
        $files     = new Files([], $page);

        foreach ($this->base()->files() as $filename => $props) {

            $file = new File([
                'filename' => $filename,
                'url'      => $url . '/' . $filename,
                'parent'   => $page,
                'store'    => static::FILE_STORE_CLASS
            ]);

            $files->set($file->id(), $file);

        }

        return $files;
    }

    public function id(): string
    {
        return $this->base()->root();
    }

    protected function moveDirectory(string $old, string $new): bool
    {
        if (Dir::move($old, $new) !== true) {
            throw new Exception('The directory could not be moved');
        }

        return true;
    }

    protected function parentRoot(): string
    {
        return dirname($this->root());
    }

    protected function root($page = null): string
    {

        if ($page === null) {
            if (is_string($this->root) === true) {
                return $this->root;
            }

            return $this->root = $this->kirby()->root('content') . '/' . $this->page()->diruri();
        }

        return $this->parentRoot() . '/' . $page->dirname();
    }

    public function template(): string
    {
        return $this->base()->type();
    }

    public function update(array $values = [], array $strings = [])
    {
        $page = parent::update($values, $strings);

        if ($this->exists() === false) {
            return $page;
        }

        $this->base()->write($page->content()->toArray());

        return $page;
    }

}
