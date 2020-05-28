# Import plugin

Helper plugin to simplify the import of data from php, yaml and xml files.

You need to create your own import class like this:

    <?php namespace YourNamespace\YourPlugin\Classes;

    use Utopigs\Import\Classes\Import as BaseImport;

    class Import extends BaseImport
    {
        public function __construct()
        {
            $this->path = base_path('import/');
        }

        public function importCategories()
        {
            $result = $this->loadImportFile('categories.yaml');

            $this->importModel('\YourNamespace\YourPlugin\Models\Category', $result);
        }

        public function importTags()
        {
            $result = $this->loadImportFile('tags.yaml');

            $this->importModel('\YourNamespace\YourPlugin\Models\Tag', $result);
        }

        public function importPosts()
        {
            $result = $this->loadImportFile('posts.yaml');

            $this->importModel('\YourNamespace\YourPlugin\Models\Post', $result, ['tags'], ['image']);
        }
    }

Check the documentation in the plugin Utopigs\Import\Classes\Import class for information about the available parameters for this methods. If you need more advanced import options, you can copy the code in your own import class and modify it as needed.

In your website root folder you should have an 'import' folder with this content:

categories.yaml:

    - title: 'Category 1'
    - title: 'Category 2'

tags.yaml:
    - title: 'Tag 1'
    - title: 'Tag 2'
    - title: 'Tag 3'
    - title: 'Tag 4'

posts.yaml:
    -
        title: 'Post 1 title'
        text: >
            <p>Some html text</p>
            <p>More text</p>
        category_id: 1
        tags: [1,2,3]
        image: 'post1-picture.jpg'
    -
        title: 'Post 2 title'
        text: >
            <p>Some html text</p>
            <p>More text</p>
        category_id: 2
        tags: [1,2,3]
        image: 'post2-picture.jpg'

Then you can import your data like this:

    $import = new \YourNamespace\YourPlugin\Classes\Import;
    $import->importCategories();
    $import->importTags();
    $import->importPosts();
